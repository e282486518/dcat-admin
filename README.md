## 

这是一个`Dcat-Admin(2.2.3-bate)`的多语言方案, 在不改变`dcat-admin`原使用习惯的前提下, 兼容数据库多语言方案.
由于 `Dcat-Admin` 的 `Grid/Form/Show/Field` 等组件耦合度较高, 之前做了一个扩展 `e282486518/laravel-translatable` 发现各种继承链出问题(还会影响第三方的form扩展), 于是就直接fork过来重新修改了.

特别说明: 目前只测试了 `PHP8.1`, `MySQL5.7`, `Laravel9`

## 安装

先根据 `Dcat-Admin` 文档安装, 成功后替换 `composer.json` 中的内容, 并执行 `composer update` 将 `dcat/laravel-admin` 替换成 `e282486518/dcat-admin`

```json
    "require": {
        ...
        "e282486518/dcat-admin": "^2.0",
        ...
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/e282486518/dcat-admin.git"
        }
    ],
```

## 使用

**第一步: 修改数据库**

将数据库的多语言字段, 设置成 `JSON` 类型, 当然 `text/varchar`类型也没问题注意长度即可, 最好 `MySQL5.7` 以上使用`json`可以索引和查询.


**第二步: 模型修改**

```
use Illuminate\Database\Eloquent\Model;
use Dcat\Admin\Traits\HasTranslations;

class Test extends Model
{
    // 多语言trait
    use HasTranslations;
    
    // 需要多语言支持的字段
    public array $translatable = ['title', 'desc'];
    
    // ...
}
```

**第三步: 配置文件, 语言文件修改**

配置文件, 将 `translatable.php` 复制到 `/config/` 目录中. 并配置.

```
// 设置后台form展示方式, 一种是 tab 模式, 一种是line模式
'locale_form' => 'line', // tab/line

// 设置当前支持哪些语言
'locale_array' => [
    'zh_CN' => '中文',
    'en' => 'English'
],
```

语言文件, 配置 `/lang/` 目录下的模型语言文件, 支持的语言文件最好都设置.


## 截图

DEMO: sql文件
```
CREATE TABLE `yw_test` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` json DEFAULT NULL,
  `desc` json DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

insert  into `yw_test`(`id`,`title`,`desc`,`status`) values 
(1,'{\"en\": \"test title\", \"zh_CN\": \"中文 标题1\"}','{\"en\": \"test desc\", \"zh_CN\": \"中文描述1\"}',1),
(2,'{\"en\": \"test title english\", \"zh_CN\": \"测试中文标题\"}','{\"en\": \"test desc english\", \"zh_CN\": \"测试中文描述\"}',1);

```

DEMO: /app/Models/Test.php
```
<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\HasTranslations; //⭐
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasTranslations; //⭐

    use HasDateTimeFormatter;
    protected $table = 'yw_test';
    public $timestamps = false;


    // 需要多语言支持的字段
    public array $translatable = ['title', 'desc']; //⭐

}
```

DEMO: /app/Admin/Controllers/TestController.php 基本不需要修改
```
<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\YTest;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Form;
use Dcat\Admin\Http\Controllers\AdminController;

class TestController extends AdminController
{
    protected function grid()
    {
        //App::setLocale('zh_CN');
        return Grid::make(new YTest(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('title');
            $grid->column('desc');
            $grid->column('status');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new YTest(), function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('desc');
            $show->field('status');
        });
    }

    protected function form()
    {
        return Form::make(new YTest(), function (Form $form) {
            $form->setLocaleForm('line'); //⭐
            $form->display("id");
            $form->text("title")->required();
            $form->text("desc");
            $form->text("status");
        });
    }
}
```

![列表1](https://raw.githubusercontent.com/e282486518/laravel-translatable/master/preview/index-cn.png)
![列表2](https://raw.githubusercontent.com/e282486518/laravel-translatable/master/preview/index-en.png)
![编辑1](https://raw.githubusercontent.com/e282486518/laravel-translatable/master/preview/edit-cn.png)
![编辑2](https://raw.githubusercontent.com/e282486518/laravel-translatable/master/preview/edit-en.png)
![编辑3](https://raw.githubusercontent.com/e282486518/laravel-translatable/master/preview/edit-line.png)
![显示1](https://raw.githubusercontent.com/e282486518/laravel-translatable/master/preview/show.png)

### 鸣谢

+ [Laravel](https://laravel.com/)
+ [jqhph/dcat-admin](https://github.com/jqhph/dcat-admin)
+ [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable)


