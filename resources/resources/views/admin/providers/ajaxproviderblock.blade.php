 <option disabled selected value="">-- select --</option>
 <?php foreach($blocks as $val) { ?>
 <option value="{{ $val->id}}">{{$val->name}}</option>
<?php } ?>      