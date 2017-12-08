<?php ?>
<!-- Código do Google para tag de remarketing -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = <?php esc_attr_e($gar_code); ?>;

var google_custom_params = window.google_tag_params;

var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/<?php esc_attr_e($gar_code); ?>/?guid=ON&amp;script=0"/>
</div>
</noscript>
<!-- Fim do Código do Google para tag de remarketing -->
<?php ?>