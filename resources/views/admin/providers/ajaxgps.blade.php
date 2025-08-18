 <option disabled selected value="">-- select Gp--</option>
 <?php foreach($gpslist as $val) { ?>
 <option value="{{ $val->gp_name }}" rel="{{ $val->latitude }}" rel1="{{ $val->longitude }}">{{$val->gp_name}}</option>
<?php } ?>      