 <option disabled selected value="">-- select --</option>
 <?php foreach($blocks as $val) { ?>
 <option value="{{ $val->name }}" rel="{{ $val->id }}">{{$val->name}}</option>
<?php } ?>      