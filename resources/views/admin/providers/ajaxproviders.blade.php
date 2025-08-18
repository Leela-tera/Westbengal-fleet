 <option disabled selected value="">-- select Person --</option>
 <?php foreach($providers as $val) { ?>
 <option value="{{ $val->id}}">{{$val->first_name}} {{$val->last_name}}</option>
<?php } ?>      