<?php
/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Types1.6b3-CRED1.3b3/toolset-forms/templates/metaform-item.php $
 * $LastChangedDate: 2014-06-04 15:11:09 +0000 (Wed, 04 Jun 2014) $
 * $LastChangedRevision: 23155 $
 * $LastChangedBy: marcin $
 *
 */
if ( is_admin() ) {
?>
<div class="js-wpt-field-item wpt-field-item">
    <?php echo $out; ?>
    <?php if ( @$cfg['repetitive'] ): ?>
        <div class="wpt-repctl">
            <div class="js-wpt-repdrag wpt-repdrag">&nbsp;</div>
            <a class="js-wpt-repdelete button-secondary"><?php printf(__('Delete %s'), strtolower( $cfg['title'])); ?></a>
        </div>
    <?php endif; ?>
</div>
<?php
} else {
    if ( $cfg['repetitive'] ) {
        echo '<div class="wpt-repctl">';
    }
    echo $out;
    if ( $cfg['repetitive'] ) {
        //    echo '<div class="js-wpt-repdrag wpt-repdrag">&nbsp;</div>';
        echo '<a class="js-wpt-repdelete button-secondary">';
        printf(__('Delete %s'), strtolower( $cfg['title']));
        echo '</a>';
        echo '</div>';
    }
}
