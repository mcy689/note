

## Sublime Text 3安装插件


### 使用Package Control安装组件

安装“Package Control Package Control”，
建议使用官方安装命令：https://sublime.wbond.net/installation

按Ctrl+`调出console，粘贴以下代码到底部命令行并回车：

```
import urllib.request,os,hashlib; h = '2915d1851351e5ee549c20394736b442' + '8bc59f460fa1548d1514676163dafc88'; pf = 'Package Control.sublime-package'; ipp = sublime.installed_packages_path(); urllib.request.install_opener( urllib.request.build_opener( urllib.request.ProxyHandler()) ); by = urllib.request.urlopen( 'http://packagecontrol.io/' + pf.replace(' ', '%20')).read(); dh = hashlib.sha256(by).hexdigest(); print('Error validating download (got %s instead of %s), please try manual install' % (dh, h)) if dh != h else open(os.path.join( ipp, pf), 'wb' ).write(by)
```

重启Sublime Text 3。如果在Perferences->package settings中看到package control这一项，则安装成功。按下Ctrl+Shift+P调出命令面板输入install 调出 Install Package 选项并回车，然后在列表中选中要安装的插件。


### SublimeLinter
SublimeLinter 是前端编码利器——Sublime Text 的一款插件，用于高亮提示用户编写的代码中存在的不规范和错误的写法，支持 JavaScript、CSS、HTML、Java、PHP、Python、Ruby 等十多种开发语言。这篇文章介绍如何在 Windows 中配置 SublimeLinter 进行 JS & CSS 校验。
比如写例如像lua这样的弱语言脚本代码，有这个可以规避掉很多不该有的低级错误吧？当然这也需要你SublimeLinter安装完毕之后再安装一个SublimeLinter-lua即可。

具体的使用可以参见：[借助 SublimeLinter 编写高质量的 JavaScript & CSS 代码](http://www.cnblogs.com/lhb25/archive/2013/05/02/sublimelinter-for-js-css-coding.html)


在Sublime3中，
SublimeLinter的检测插件被独立了出来，
也就是说我们除了**==SublimeLinter==**本体以外，
我们还需要在Package Control中安装
**==Sublimelinter-php==**, 
**==Sublimelinter-jshint==**, 
**==Sublimelinter-csslint==**
这三个插件。


装完之后，还需要用nodejs安装jshint和csslint，使用如下命令。（没有安装nodejs请先安装nodejs）

```
npm install -g jshint
npm install -g csslint
```

可以使用如下命令检查这两个插件是否安装完成

```
npm install -g jshint
csslint --version
```
在[Preferences]-> [Package Settings]->[SublimeCodeIntel]->[Settings-User]


```
{
    "user": {
        "debug": false,
        "delay": 0.25,
        "error_color": "D02000",
        "gutter_theme": "Packages/SublimeLinter/gutter-themes/Default/Default.gutter-theme",
        "gutter_theme_excludes": [],
        "lint_mode": "background",
        "linters": {
            "csslint": {
                "@disable": false,
                "args": [],
                "errors": "",
                "excludes": [],
                "ignore": "",
                "warnings": ""
            },
            "jshint": {
                "@disable": false,
                "args": [],
                "excludes": []
            },
            "php": {
                "@disable": false,
                "args": [],
                "excludes": []
            }
        },
        "mark_style": "outline",
        "no_column_highlights_line": false,
        "passive_warnings": false,
        "paths": {
            "linux": [],
            "osx": [],
            "windows": [
                "D:\\Server\\wamp\\bin\\php\\php5.3.10\\php.exe"
            ]
        },
        "python_paths": {
            "linux": [],
            "osx": [],
            "windows": []
        },
        "rc_search_limit": 3,
        "shell_timeout": 10,
        "show_errors_on_save": true,
        "show_marks_in_minimap": true,
        "syntax_map": {
            "html (django)": "html",
            "html (rails)": "html",
            "html 5": "html",
            "javascript (babel)": "javascript",
            "php": "html",
            "python django": "python"
        },
        "warning_color": "DDB700",
        "wrap_find": true
    }
}
```
其中paths项，需要把PHP的路径放进去，show_errors_on_save可以控制是否在保存的时候提示错误，这里我选择了打开


### SublimeCodeIntel
一个全功能的 Sublime Text 代码自动完成引擎

配置文件SublimeCodeIntel.sublime-settings
在[Preferences]-> [Package Settings]->[SublimeCodeIntel]->[Settings-Default] 
中的PHP的两个路径"**php**"和"**codeintel_scan_exclude_dir**"
```
"PHP": {
    "php": "D:\\Server\\wamp\\bin\\php\\php5.5.12\\php.exe",
    "codeintel_scan_extra_dir": [],
    "codeintel_scan_files_in_project": true,
    "codeintel_max_recursive_dir_depth": 15,
    "codeintel_scan_exclude_dir":["D:\\Server\\wamp\\bin\\php\\php5.5.12"]
}
```

### Emmet

Emmet 项目的前身是前端开发人员熟知的 Zen Coding（快速编写 HTML/CSS 代码的方案）。在 Sublime Text 编辑器中搭配 Emmet 插件真的是让你编码快上加快。


### LESS
LESS代码高亮


### CSScomb
CSScomb 插件的主要特征：
1. 帮助排序CSS属性
2. 自定义排序规则
3. 可以处理标签 style 内的CSS属性
4. 格式不变化
5. 完全支持CSS2/CSS2.1/CSS3和CSS4

CSS属性排序使用方法：
选中要排序的CSS代码，按Ctrl+Shift+C。

CSScomb 插件需要 Node.js 的支持==，所以要想 CSScomb 起作用，你还必需安装 Node.js，
Node.js 官方下载地址：https://nodejs.org

打开插件目录里的CSScomb.sublime-settings文件，更改里面的CSS属性顺序就行了。


报错处理

```
CSScomb error:
C://Users\123\Desktop\Subime Text Build3083\Data\Packages\CSScomb\node_modules\csscomb\node_modules\csscomb-core\lib\core.js:412
throw e;
Please check the validity of the block starting from line #1
```

你可以在Sublime Text 选项 
[Preferences]-> [Package Settings]->[CSScomb]->[Settings-Default] 
找到CSScomb.sublime-settings文件把下面这行代码：

```
"node-path" : ":/usr/local/bin"
```

这个路径改成你安装Node.js里的bin文件的路径如我的：

```
"node-path" : "C:\\Program Files\\nodejs\\node_modules\\npm\\bin"
```




### Javascript-API-Completions
支持Javascript、JQuery、Twitter Bootstrap框架、HTML5标签属性提示的插件，是少数支持sublime text 3的后缀提示的插件，HTML5标签提示sublime text3自带，不过JQuery提示还是很有用处的，也可设置要提示的语言。

### DocBlockr
注释规范

打开Preferences -> Package Settings -> DocBlockr->Settings - User 并新建一个User配置文件,


```
{
    "jsdocs_extra_tags":["@author Joe(spzgy03@gmail.com) {{datetime}}"]
}
```

### JsFormat 格式JS文件
插件名字：JsFormat
格式效果很不错，比Minify要好。

### Minify压缩,格式化css,js,html,json,svg

1. 通过 Package Control 安装Minify

按 ctrl + shift + p   输入  Install Package 然后   输入Minify  按回车就可以安装啦

2. 安装note.js

安装合成后打开cmd输入   node --version 如果出现版本信息就可以啦,否则的话就把nodejs的路径添加到系统环境变量里去;

3. 打开cmd输入下面命令安装


```
npm install -g clean-css-cli uglifycss js-beautify html-minifier uglify-js minjson svgo
```
如果你已经安装过其中的一些的话可以用下面命令更新


```
npm update -g clean-css-cli uglifycss js-beautify html-minifier uglify-js minjson svgo
```


4. 开始使用
使用ctrl + alt + m 压缩文件 会生成一个新的min文件

使用ctrl + alt + shift + m 格式化文件
5. 自定义（示例）

```
{
    "settings": {
        "Minify": {
            "open_file": false,
            "auto_minify_on_save": true,
            "allowed_file_types": [
                "css",
                "js",
                "svg"
            ]
        }
    }
}
```




### 修改侧边栏大小
1. 安装“Package Control Package Control”，建议使用官方安装命令：https://sublime.wbond.net/installation
2. 安装”PackageResourceViewer”
3. Ctrl+Shift+P，搜索”PackageResourceViewer: Open Resource”
4. 搜索”Theme – Default”
5. 搜索”Default.sublimt-theme”
6. 在”sidebar_label”,”后面一行加上 "font.size": 13,
7. 在sidebar_label后面加入一行: "font.face":"microsoft yahei",
8. 如果觉得行间距太小，可以往上找下，有个"sidebar_tree",，调一下里边的"row_padding"值即可。"row_padding": [8, 5],
9. 修改标签栏在"tab_label" 后面加入一行: "font.size" : 12,  "font.face":"microsoft yahei",


### 注册吗
```
Alexey Plutalov
Single User License
EA7E-860776
3DC19CC1 134CDF23 504DC871 2DE5CE55
585DC8A6 253BB0D9 637C87A2 D8D0BA85
AAE574AD BA7D6DA9 2B9773F2 324C5DEF
17830A4E FBCF9D1D 182406E9 F883EA87
E585BBA1 2538C270 E2E857C2 194283CA
7234FF9E D0392F93 1D16E021 F1914917
63909E12 203C0169 3F08FFC8 86D06EA8
73DDAEF0 AC559F30 A6A67947 B60104C6
```

### 系统配置
在[Preferences]->[Settings-User] 

```
"font_face": "microsoft yahei",
"tab_size": 2,
```


