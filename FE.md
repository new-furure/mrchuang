##关于前端

###CSS

- 用sass来写CSS
- 有通用base。其他拆分页面，各个页面自己的CSS
- bem命名。大的部分可以用id，其他的用class为主，少些特别特殊的选择器，需要考虑兼容性
- 前端兼容到IE6，上线前需要本地虚拟机测试低版本IE
- 通过[W3C验证](http://jigsaw.w3.org/css-validator/validator.html.en)

###JS

- jslint检查
- 用JQUERY作为JS框架
- 写自己的基础库，精简内容
- 注意性能和执行顺序
- JS页需要考虑兼容性，兼容到IE6

###HTML

- 语义化
- 精简节点
- 通过[W3C验证](http://validator.w3.org/)

###性能

前端性能非常关键，会直接影响很大部分的用户体验。前端性能的影响因素也很多，例如资源的加载、页面结构、JS阻塞等等，各种内容都需要优化，并且简历性能监控机制。平时开发过程中，工程师需要多考虑性能的问题。

###图片

基本分3类，通用图片、页面图片、sp。其中需要作为CSS背景的放在css的子目录中，例如i之类的，sp都用sass来拼图。sp也分较为通用的和单个页面的。图标用[fontawesome](http://fontawesome.io/)

###combo

CSS和JS的资源请求由combo服务来进行合并。需要注意缓存和并发。
