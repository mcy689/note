### Sublime Text3 Package Control 在菜单栏中不显示

#### 前言

```
最近由于在 Sublime Text3 下配置了React 开发环境，最近也更新了Sublime Text3 的版本，由此装上了很多的插件。今天打开Sublime 想要通过 `Package Control` 装个东西，一看 `Package Control` 不见了。
```

#### 折腾

```
我想可能是因为更新版本导致的，然后重新装 `Package Control` ,但还是看不见。 折腾了一会，卸载，然后重装 Sublime ，在装`Package Control` 这时候就可以了。但是本身好多插件我也不想重新安装，于是把原有的Package包复制过来，让我郁闷的事情又来了，`Package Control` 又看不见了。复制的时候会有几个同名的文件，我直接替换了。于是我挨个文件排查，终于找到了。
```

#### 解决问题

```
在 `C:\Users\*****\AppData\Roaming\Sublime Text 3\Packages\User` 目录下有个 `Preferences.sublime-settings` 文件，内容为

{
"color_scheme": "Packages/Babel/Monokai Phoenix.tmTheme",
"font_size": 14,
"ignored_packages":
[
    "Vintage",
    "Package Control"
],
"word_wrap": true
}


看到有个 `ignored_packages` 这一项的 `Package Control` 去掉，问题得易解决。

修改后的内容：
{
"color_scheme": "Packages/Babel/Monokai Phoenix.tmTheme",
"font_size": 14,
"ignored_packages":
[
    "Vintage"
],
"word_wrap": true
}
```

### 结束语

```
因为这一个小小的问题，让我折腾了两个小时，在这记录一下，如果其他小伙伴有遇到这种情况，方便查看。
```