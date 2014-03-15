#关于代码的个人意见

__我可能对前端部分的看法多一些，欢迎修改和补充__

##PHP

- 变量名首字母小写，驼峰，表意，尽量别用tmp之类的。少用拼音，缩写尽量易懂
- 布尔用`is`、`can`等开头
- 函数名小写单词加下划线分割
- phplint检查（或者用php的`-l`）

##JS

- 变量名首字母小写，驼峰，表意，尽量别用tmp之类的。少用拼音，缩写尽量易懂
- 函数名驼峰
- 布尔用`is`、`can`等开头
- 节点用nd开头，节点列表用nl开头
- jslint检查
- 对外接口需要注释
    - 简单注释用`//`单行说明
    - 复杂注释用以下格式

```
/**
 * @method testFunction
 * @description 功能描述
 * @param {Object} objParam 参数描述
 * @param {Number} num 参数描述2
 */
作者什么的，感觉不用写了，维护人员较多，git也完全能查出来
```

##CSS

- 类名小写单词，`-`分割
- css采用[sass](http://sass-lang.com/)编译
- 所有给JS实用的选择器以`J-`开头
- css采用[BEM命名法](http://www.w3cplus.com/css/mindbemding-getting-your-head-round-bem-syntax.html)
- 选择器的选择上，对于id和标签，需要谨慎一点

