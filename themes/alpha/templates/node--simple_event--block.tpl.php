<div id="node-<?php print $node->nid; ?>"  class="row  <?php if(isset($classes)): print $classes; endif;?> <?php print $zebra;?> "<?php if(isset($attributes)): print $attributes; endif;?>>
  <div class="col-xs-1 image">
    <?php print render($content['event_image']); ?>
  </div>
  <?php if (isset($content['address'])): ?>
    <div class="col-xs-3 title">
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
      <div class="event-type">
        <?php print render($content['simple_event_type']); ?>
      </div>
    </div>
    <div class="col-xs-4 title">
        <div class="address">
          <?php print render($content['address']); ?>
        </div>
    </div>
  <?php else: ?>
    <div class="col-xs-7 title">
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
      <div class="event-type">
        <?php print render($content['simple_event_type']); ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="col-xs-4 date_time">
    <?php print render($content['datetime']); ?>
  </div>
</div>
  
  