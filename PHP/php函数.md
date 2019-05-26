#### PHP函数

1. `register_shutdown_function` — 注册一个会在php中止时执行的函数

   * 注册一个 `callback` ，它会在脚本执行完成或者 `exit()` 后被调用。

     可以多次调用 **register_shutdown_function()** ，这些被注册的回调会按照他们注册时的顺序被依次调用。 如果你在注册的方法内部调用 `exit()`， 那么所有处理会被中止，并且其他注册的中止回调也不会再被调用。

   ```php

   ```

   ​

