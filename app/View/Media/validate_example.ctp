<?php 

echo __('Are you sure you want to create an entry for Example ticket %s?', $media['MediaDetail']['ticket_ticket']);
echo "\n\n";
echo __('A MediaTracker entry already exists for this ticket number entered on %s by %s.', $this->Wrap->niceTime($media['Media']['created']), $media['MediaAddedUser']['name']);
