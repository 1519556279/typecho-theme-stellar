<?php if(!defined('__TYPECHO_ADMIN__')) exit; ?>
<?php \Typecho\Plugin::factory('admin/footer.php')->call('begin'); ?>
    </div><!-- end .sa-shell -->
</div><!-- end .sa-app -->
    </body>
</html>
<?php
/** 注册一个结束插件 */
\Typecho\Plugin::factory('admin/footer.php')->call('end');
