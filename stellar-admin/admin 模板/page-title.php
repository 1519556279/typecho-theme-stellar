<?php if (!defined('__TYPECHO_ADMIN__')) exit; ?>
<div class="sa-page-head">
    <h2><?php echo $menu->title ?? ''; ?></h2>
    <?php
    if (!empty($menu->addLink)) {
        echo "<a class=\"btn btn-primary\" href=\"" . $menu->addLink . "\"><span class=\"sa-plus\">+</span>" . _t("新增") . "</a>";
    }
    ?>
</div>
