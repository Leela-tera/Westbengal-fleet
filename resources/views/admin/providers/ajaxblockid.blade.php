 <option disabled selected value="">-- select --</option>
 <?php foreach($blocks as $val) { ?>
 <option value="{{ $val->id}}" rel="{{ $val->id }}">{{$val->name}}</option>
<?php } ?>      