<?php

function publish_action($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE base_fields SET `bool` = b\'1\' WHERE id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function unpublish_action($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE base_fields SET `bool` = b\'0\' WHERE id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function add_currency($value, $fieldname, $primary_key, $row, $xcrud) {
    $ci = & get_instance();
    return $ci->session->userdata('userdata')['currency_symbol'] . $value;
}

function exception_example($postdata, $primary, $xcrud) {
    // get random field from $postdata
    $postdata_prepared = array_keys($postdata->to_array());
    shuffle($postdata_prepared);
    $random_field = array_shift($postdata_prepared);
    // set error message
    $xcrud->set_exception($random_field, 'This is a test error', 'error');
}

function test_column_callback($value, $fieldname, $primary, $row, $xcrud) {
    return $value . ' - nice!';
}

function after_upload_example($field, $file_name, $file_path, $params, $xcrud) {
    $ext = trim(strtolower(strrchr($file_name, '.')), '.');
    if ($ext != 'pdf' && $field == 'uploads.simple_upload') {
        unlink($file_path);
        $xcrud->set_exception('simple_upload', 'This is not PDF', 'error');
    }
}

function movetop($xcrud) {
    if ($xcrud->get('primary') !== false) {
        $primary = (int) $xcrud->get('primary');
        $db = Xcrud_db::get_instance();
        $query = 'SELECT `officeCode` FROM `offices` ORDER BY `ordering`,`officeCode`';
        $db->query($query);
        $result = $db->result();
        $count = count($result);

        $sort = array();
        foreach ($result as $key => $item) {
            if ($item['officeCode'] == $primary && $key != 0) {
                array_splice($result, $key - 1, 0, array($item));
                unset($result[$key + 1]);
                break;
            }
        }

        foreach ($result as $key => $item) {
            $query = 'UPDATE `offices` SET `ordering` = ' . $key . ' WHERE officeCode = ' . $item['officeCode'];
            $db->query($query);
        }
    }
}

function movebottom($xcrud) {
    if ($xcrud->get('primary') !== false) {
        $primary = (int) $xcrud->get('primary');
        $db = Xcrud_db::get_instance();
        $query = 'SELECT `officeCode` FROM `offices` ORDER BY `ordering`,`officeCode`';
        $db->query($query);
        $result = $db->result();
        $count = count($result);

        $sort = array();
        foreach ($result as $key => $item) {
            if ($item['officeCode'] == $primary && $key != $count - 1) {
                unset($result[$key]);
                array_splice($result, $key + 1, 0, array($item));
                break;
            }
        }

        foreach ($result as $key => $item) {
            $query = 'UPDATE `offices` SET `ordering` = ' . $key . ' WHERE officeCode = ' . $item['officeCode'];
            $db->query($query);
        }
    }
}

function show_description($value, $fieldname, $primary_key, $row, $xcrud) {
    $result = '';
    if ($value == '1') {
        $result = '<i class="fa fa-check" />' . 'OK';
    } elseif ($value == '2') {
        $result = '<i class="fa fa-circle-o" />' . 'Pending';
    }
    return $result;
}

function custom_field($value, $fieldname, $primary_key, $row, $xcrud) {
    return '<input type="text" readonly class="xcrud-input" name="' . $xcrud->fieldname_encode($fieldname) . '" value="' . $value .
    '" />';
}

function unset_val($postdata) {
    $postdata->del('Paid');
}

function format_phone($new_phone) {
    $new_phone = preg_replace("/[^0-9]/", "", $new_phone);

    if (strlen($new_phone) == 7)
        return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $new_phone);
    elseif (strlen($new_phone) == 10)
        return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $new_phone);
    else
        return $new_phone;
}

function before_list_example($list, $xcrud) {
    var_dump($list);
}

function soft_delete($primary, $xcrud) {
    $db = Xcrud_db::get_instance();
    $db->query("UPDATE " . $xcrud->table . " set deleted_at='" . date("Y-m-d h:i:s") . "' Where id = " . $db->escape($primary));
}

function soft_delete_payroll_groups($primary, $xcrud) {
    $db = Xcrud_db::get_instance();
    $db->query("UPDATE " . $xcrud->table . " set deleted_at='" . date("Y-m-d h:i:s") . "' Where id = " . $db->escape($primary));
}

function form_category_delete($primary, $xcrud) {
    $db = Xcrud_db::get_instance();
    $query = "SELECT * FROM sh_form_categories WHERE id =" . $primary;
    $db->query($query);
    if ($db->row()["tag"] == 'is_system') {
        $xcrud->set_exception('tag', lang('system_cat_validate'), 'error');
    } else {
        $query = "UPDATE sh_form_categories SET deleted_at='" . date('Y-m-d h:i:s') . "' WHERE id=" . $primary;
        $db->query($query);
    }
}

function primaryicon($value, $fieldname, $primary_key, $row, $xcrud) {
    return $value == "Y" ? '<i class="fa fa-check"></i>' : "";
}

function status($value, $fieldname, $primary_key, $row, $xcrud) {
    return $value == 1 ? "Active" : "Disable";
}

function deleteOtherPrimary($postdata, $xcrud) {
    $start = $postdata->get('start_date');
    $end = $postdata->get('end_date');
    $query = "select count(case when CAST('$start' AS date) <= end_date and CAST('$end' AS date) >= start_date then 1 end) as overlap from sh_academic_years where school_id=".$postdata->get('school_id')." and deleted_at is null";
    $db = Xcrud_db::get_instance();
    $db->query($query);
    $overlap = $db->row()['overlap'];
    if ($postdata->get('end_date') < $postdata->get('start_date')) {
        $xcrud->set_exception('end_date', lang('date_xcrud'));
    } else if($postdata->get('end_date') == $postdata->get('start_date')){
        $xcrud->set_exception('end_date', lang('start_date_same'));
    } else if($overlap > 0){
        $xcrud->set_exception('start_date', lang('academic_year_overlap'));
    } else if ($postdata->get('is_active') == "Y") {
        $db = Xcrud_db::get_instance();
        $query = "select * from sh_academic_years where school_id =" . $postdata->get('school_id') . " and is_active='Y' ";
        $db->query($query);
        foreach ($db->result() as $val) {
            $query = "update sh_academic_years set is_active='N' Where id=" . $val["id"];
            $db->query($query);
        }
    }
}

function check_evaluation($postdata, $xcrud) {
    $type = $postdata->get('type');
    $classes = $postdata->get('classes');
    $classes = explode(",", $classes);
    foreach ($classes as $key => $value) {
        $db = Xcrud_db::get_instance();
        $query = "select id from sh_evaluations where type ='" . $type . "' and find_in_set(".$value.",classes) > 0 and deleted_at is null";
        $db->query($query);
        if(count($db->result()) > 0){
            $query = "select name from sh_classes where id = ".$value;
            $db->query($query);
            $class = $db->row()['name'];
            $xcrud->set_exception('classes', $type.' evaluation type already exist for '.$class.'.');
            break;
        }
    }
    

    
}

function check_evaluation_update($postdata, $primary, $xcrud) {
    $type = $postdata->get('type');
    $classes = $postdata->get('classes');
    $classes = explode(",", $classes);
    $db = Xcrud_db::get_instance();
    foreach ($classes as $key => $value) {
        
        $query = "select id from sh_evaluations where type ='" . $type . "' and find_in_set(".$value.",classes) > 0 and deleted_at is null and id <> ".$primary;
        $db->query($query);
        if(count($db->result()) > 0){
            $query = "select name from sh_classes where id = ".$value;
            $db->query($query);
            $class = $db->row()['name'];
            $xcrud->set_exception('classes', $type.' evaluation type already exist for '.$class.'.');
            break;
        }
    }
    

    
}

function checkValidation($postdata, $xcrud) {

    $from = $postdata->get('percent_from');
    $to = $postdata->get('percent_upto');
    if ($from >= 0 && $from <= 100) {

    } else {
        $xcrud->set_exception('percent_from', lang('grade_validation'));
    }

    if ($to >= 0 && $to <= 100) {

    } else {
        $xcrud->set_exception('percent_upto', lang('grade_validation'));
    }
}

function checkValidationUpdate($postdata, $primary, $xcrud) {

    $from = $postdata->get('percent_from');
    $to = $postdata->get('percent_upto');
    if ($from >= 0 && $from <= 100) {

    } else {
        $xcrud->set_exception('percent_from', lang('grade_validation'));
    }

    if ($to >= 0 && $to <= 100) {

    } else {
        $xcrud->set_exception('percent_upto', lang('grade_validation'));
    }
}

function addSection($postdata, $xcrud) {
    $db = Xcrud_db::get_instance();
    $query = "insert into sh_batches(school_id,name,academic_year_id,class_id,teacher_id) values(" . $postdata->get('school_id') . ",'" . $postdata->get('name') . "'," . $postdata->get('academic_year_id') . ",'" . $postdata->get('class_id') . "','" .  $postdata->get('teacher_id') . "')";
    $db->query($query);
}

function subjectsAdd($postdata, $xcrud) {
    if ($postdata->get('batch_id') == "") {

        $db = Xcrud_db::get_instance();
        $query = "select id from sh_batches where school_id =" . $postdata->get('school_id') . " and class_id =" . $postdata->get('class_id') . " and deleted_at is null ";
        $db->query($query);
        foreach ($db->result() as $val) {
            $query = "insert into sh_subjects(school_id,class_id,batch_id,name,code,weekly_classes,academic_year_id) values(" . $postdata->get('school_id') . "," . $postdata->get('class_id') . "," . $val["id"] . ",'" . $postdata->get('name') . "','" . $postdata->get('code') . "'," . $postdata->get('weekly_classes') . ",".$postdata->get('academic_year_id').")";
            $db->query($query);
        }
    } else {
        $query = "insert into sh_subjects(school_id,class_id,batch_id,name,code,weekly_classes,academic_year_id) values(" . $postdata->get('school_id') . "," . $postdata->get('class_id') . "," . $postdata->get('batch_id') . ",'" . $postdata->get('name') . "','" . $postdata->get('code') . "'," . $postdata->get('weekly_classes') . ",".$postdata->get('academic_year_id').")";
        $db = Xcrud_db::get_instance();
        $db->query($query);
    }
}

function check_academic_year($postdata, $xcrud) {
    $academic_year_id = $postdata->get('academic_year_id');
    if($academic_year_id == null){
        $xcrud->set_exception('', lang('no_academic_year'));
    }   
}

function section_check($postdata, $primary, $xcrud) {
    $start = $postdata->get('start_time');
    $end = $postdata->get('end_time');
    $class_id = $postdata->get('class_id');
    $batch_id = $postdata->get('batch_id');
    $school_id = $postdata->get('school_id');
    if ($postdata->get('batch_id') == "") {
        $xcrud->set_exception('batch_id', lang('choose_section'));
    } else {
        $db = Xcrud_db::get_instance();
        if ($batch_id != "") {
            $query = "select count(case when CAST('$start' AS time) < end_time and start_time < CAST('$end' AS time) then 1 end) as overlap from sh_periods where school_id=$school_id and class_id=$class_id and batch_id=$batch_id and deleted_at is null and id!=$primary";
        } else {
            $query = "select count(case when CAST('$start' AS time) < end_time and start_time < CAST('$end' AS time) then 1 end) as overlap from sh_periods where school_id=$school_id and class_id=$class_id and deleted_at is null and id!=$primary";
        }
        $db->query($query);
        $overlap = $db->row()['overlap'];

        if ($start > $end) {
            $xcrud->set_exception('end_time', lang('end_time_validation'));
        } else if ($start == $end) {
            $xcrud->set_exception('end_time', lang('start_time_validation'));
        } else if ($overlap > 0) {
            $xcrud->set_exception('end_time', lang('period_overlap'));
        }
    }
}

function periodsAdd($postdata, $xcrud) {
    if ($postdata->get('batch_id') == "") {

        $db = Xcrud_db::get_instance();
        $query = "select id from sh_batches where school_id =" . $postdata->get('school_id') . " and class_id =" . $postdata->get('class_id') . " and deleted_at is null ";
        $db->query($query);
        foreach ($db->result() as $val) {
            $query = "insert into sh_periods(school_id,title,start_time,end_time,class_id,batch_id,is_break,academic_year_id) values(" . $postdata->get('school_id') . ",'" . $postdata->get('title') . "','" . $postdata->get('start_time') . "','" . $postdata->get('end_time') . "'," . $postdata->get('class_id') . "," . $val["id"] . ",'" . $postdata->get('is_break') . "',".$postdata->get('academic_year_id').")";
            $db->query($query);
        }
    } else {
        $query = "insert into sh_periods(school_id,title,start_time,end_time,class_id,batch_id,is_break,academic_year_id) values(" . $postdata->get('school_id') . ",'" . $postdata->get('title') . "','" . $postdata->get('start_time') . "','" . $postdata->get('end_time') . "'," . $postdata->get('class_id') . "," . $postdata->get('batch_id') . ",'" . $postdata->get('is_break') . "',".$postdata->get('academic_year_id').")";
        $db = Xcrud_db::get_instance();
        $db->query($query);
    }
}

function checkOverlap($postdata, $xcrud) {
    $start = $postdata->get('start_time');
    $end = $postdata->get('end_time');
    $class_id = $postdata->get('class_id');
    $batch_id = $postdata->get('batch_id');
    $school_id = $postdata->get('school_id');


    $db = Xcrud_db::get_instance();
    if ($batch_id != "") {
        $query = "select count(case when CAST('$start' AS time) < end_time and start_time < CAST('$end' AS time) then 1 end) as overlap from sh_periods where school_id=$school_id and class_id=$class_id and batch_id=$batch_id and deleted_at is null";
    } else {
        $query = "select count(case when CAST('$start' AS time) < end_time and start_time < CAST('$end' AS time) then 1 end) as overlap from sh_periods where school_id=$school_id and class_id=$class_id and deleted_at is null";
    }
    $db->query($query);
    $overlap = $db->row()['overlap'];

    if ($start > $end) {
        $xcrud->set_exception('end_time', lang('end_time_validation'));
    } else if ($start == $end) {
        $xcrud->set_exception('end_time', lang('start_time_validation'));
    } else if ($overlap > 0) {
        $xcrud->set_exception('end_time', lang('period_overlap'));
    }
}

function new_subject_group($postdata, $xcrud){
    $batch_id = $postdata->get('batch_id');
    $class_id = $postdata->get('class_id');
    $school_id = $postdata->get('school_id');
    $group_name = $postdata->get('group_name');
    $subjects = $postdata->get('subjects');
    if($subjects[0] == ","){
        $subjects = substr($subjects,1);
    }
    $subjects = explode(",", $subjects);
    sort($subjects , SORT_NUMERIC);
    $subjects = implode(",", $subjects);
    $postdata->set('subjects',$subjects);

    $db = Xcrud_db::get_instance();
    $query = "select id from sh_subject_groups where batch_id in(" . $batch_id . ") and group_name = '". $group_name ."' and deleted_at is null ";
    $db->query($query);
    if (count($db->result()) > 0) {
        $xcrud->set_exception('group_name', 'Subject group name already exists!');
    }

    $query = "select id from sh_subject_groups where subjects ='" . $subjects . "' and batch_id in(". $batch_id .") and deleted_at is null ";
    $db->query($query);
    if (count($db->result()) > 0) {
        $xcrud->set_exception('subjects', 'Subject group already exists!');
    }

}
function new_subject_group_update($postdata, $primary, $xcrud){
    $batch_id = $postdata->get('batch_id');
    $class_id = $postdata->get('class_id');
    $school_id = $postdata->get('school_id');
    $group_name = $postdata->get('group_name');
    $subjects = $postdata->get('subjects');
    if($subjects[0] == ","){
        $subjects = substr($subjects,1);
    }
    $subjects = explode(",", $subjects);
    sort($subjects , SORT_NUMERIC);
    $subjects = implode(",", $subjects);
    $postdata->set('subjects',$subjects);

    $db = Xcrud_db::get_instance();
    $query = "select id from sh_subject_groups where batch_id IN (" . $batch_id . ") and group_name = '". $group_name ."' and deleted_at is null and id <> ".$primary;
    $db->query($query);
    if (count($db->result()) > 0) {
        $xcrud->set_exception('group_name', 'Subject group name already exists!');
    }

    $query = "select id from sh_subject_groups where subjects ='" . $subjects . "' and batch_id IN (". $batch_id .") and deleted_at is null and id <> ".$primary;
    $db->query($query);
    if (count($db->result()) > 0) {
        $xcrud->set_exception('subjects', 'Subject group already exists!');
    }

}
function section_count($postdata, $xcrud) {
    $batch_id = $postdata->get('batch_id');
    $class_id = $postdata->get('class_id');
    $school_id = $postdata->get('school_id');
    $name = $postdata->get('name');
    $code = $postdata->get('code');

    $where_name = "select id from sh_subjects where class_id=" . $class_id . " and school_id=" . $school_id . " and name='" . $name . "' and deleted_at is null";
    $where_code = "select id from sh_subjects where class_id=" . $class_id . " and school_id=" . $school_id . " and code='" . $code . "' and deleted_at is null";

    if ($batch_id == "") {
        $db = Xcrud_db::get_instance();
        $query = "select id from sh_batches where school_id =" . $school_id . " and class_id =" . $class_id . " and deleted_at is null ";
        $db->query($query);

        if (count($db->result()) == 0) {
            $xcrud->set_exception('batch_id', lang('class_validation'));
        }
        $query = $where_name;
        $db->query($query);
        if (count($db->result()) > 0) {
            $xcrud->set_exception('name', lang('sub_name_xcrud'));
        } else {
            $query = $where_code;
            $db->query($query);
            if (count($db->result()) > 0) {
                $xcrud->set_exception('code', lang('sub_code_xcrud'));
            }
        }
    } else {
        $db = Xcrud_db::get_instance();
        $query = $where_name . " and batch_id=" . $batch_id;
        $db->query($query);
        if (count($db->result()) > 0) {
            $xcrud->set_exception('name', lang('subject_name_xcrud'));
        } else {
            $query = $where_code . " and batch_id=" . $batch_id;
            $db->query($query);
            if (count($db->result()) > 0) {
                $xcrud->set_exception('code', lang('subject_code_xcrud'));
            }
        }
    }
}

function date_validation($postdata, $xcrud) {
    if ($postdata->get('end_date') < $postdata->get('start_date')) {
        $xcrud->set_exception('end_date', lang('date_xcrud'));
    }
}

function section_check_subject($postdata, $primary, $xcrud) {
    $batch_id = $postdata->get('batch_id');
    if ($postdata->get('batch_id') == "") {
        $xcrud->set_exception('batch_id', lang('choose_section'));
    }
}

function add_user_icon($value, $fieldname, $primary_key, $row, $xcrud) {
    return '<button type="button" ng-click="getDiscountVarients(' . $primary_key . ')" class="btn btn-default" data-toggle="modal" data-target="#feeDiscountVarientModal">Varient</button>';
}

function remove_link($postdata, $xcrud) {
    $price = $postdata->get('price');
    $postdata->set('link',preg_replace('(http[s]?:[/]?[/]?)', '', $postdata->get('link')));
    if($price < 0){
        $xcrud->set_exception('price', lang('book_shop_price'));
    }
}
function remove_link_update($postdata, $primary, $xcrud) {
    $price = $postdata->get('price');
    $postdata->set('link',preg_replace('(http[s]?:[/]?[/]?)', '', $postdata->get('link')));
    if($price < 0){
        $xcrud->set_exception('price', lang('book_shop_price'));
    }
}

function check_exam_session($postdata, $xcrud) {
    $start = $postdata->get('start_date');
    $end = $postdata->get('end_date');


    if ($start > $end) {
        $xcrud->set_exception('end_date', lang('date_xcrud'));
    }
}

function check_exam_session_update($postdata, $primary, $xcrud) {
    $start = $postdata->get('start_date');
    $end = $postdata->get('end_date');


    if ($start > $end) {
        $xcrud->set_exception('end_date', lang('date_xcrud'));
    }
}
function update_academic_year($postdata, $primary, $xcrud) {
    $ci = & get_instance();
    $result = $ci->db->select('id,name')->from('sh_academic_years')->where('is_active',"Y")->where('school_id',$ci->session->userdata("userdata")["sh_id"])->get()->row();
    $new_id = 0;
    $new_name = "-";
    if(count($result) > 0){
        $new_id = $result->id;
        $new_name = $result->name;
    }

    $oldValues = $ci->session->userdata("userdata");
    $oldValues["academic_year"] = $new_id;
    $oldValues["academic_year_name"] = $new_name;
    $ci->session->set_userdata("userdata",$oldValues);
    reloadPage($primary, $xcrud);


}

function update_academic_year_delete($primary, $xcrud) {
    $ci = & get_instance();
    $result = $ci->db->select('id,name')->from('sh_academic_years')->where('is_active',"Y")->where('school_id',$ci->session->userdata("userdata")["sh_id"])->where('deleted_at is null')->get()->row();
    $new_id = 0;
    $new_name = "-";
    if(count($result) > 0){
        $new_id = $result->id;
        $new_name = $result->name;
    }

    $oldValues = $ci->session->userdata("userdata");
    $oldValues["academic_year"] = $new_id;
    $oldValues["academic_year_name"] = $new_name;
    $ci->session->set_userdata("userdata",$oldValues);
    reloadPage($primary, $xcrud);


}

function deleteOtherPrimary_update($postdata, $primary, $xcrud) {
    $start = $postdata->get('start_date');
    $end = $postdata->get('end_date');
    $query = "select count(case when CAST('$start' AS date) <= end_date and CAST('$end' AS date) >= start_date then 1 end) as overlap from sh_academic_years where school_id=".$postdata->get('school_id')." and deleted_at is null and id!=$primary";
    $db = Xcrud_db::get_instance();
    $db->query($query);
    $overlap = $db->row()['overlap'];
    if ($postdata->get('end_date') < $postdata->get('start_date')) {
        $xcrud->set_exception('end_date', lang('date_xcrud'));
    } else if($postdata->get('end_date') == $postdata->get('start_date')){
        $xcrud->set_exception('end_date', lang('start_date_same'));
    } else if($overlap > 0){
        $xcrud->set_exception('start_date', lang('academic_year_overlap'));
    } else if ($postdata->get('is_active') == "Y") {
        $db = Xcrud_db::get_instance();
        $query = "select * from sh_academic_years where school_id =" . $postdata->get('school_id') . " and is_active='Y' ";
        $db->query($query);
        foreach ($db->result() as $val) {
            $query = "update sh_academic_years set is_active='N' Where id=" . $val["id"];
            $db->query($query);
        }
    }
}

function date_validation_update($postdata, $primary, $xcrud) {
    if ($postdata->get('end_date') < $postdata->get('start_date')) {
        $xcrud->set_exception('end_date', lang('date_xcrud'));
    }
}

function check_exam_details($postdata, $xcrud) {
    $ci = & get_instance();
    $exam_id = $postdata->get('exam_id');
    $class_id = $postdata->get('class_id');
    $batch_id = $postdata->get('batch_id');
    $subject_id = $postdata->get('subject_id');
    $exam_date = $postdata->get('exam_date');
    $start_time = $postdata->get('start_time');
    $end_time = $postdata->get('end_time');
    $total_marks = $postdata->get('total_marks');
    $passing_marks = $postdata->get('passing_marks');

    $db = Xcrud_db::get_instance();
    $query = "select id from sh_exam_details where subject_id =" . $subject_id . " and exam_id=".$exam_id." and deleted_at is null ";
    $db->query($query);
    $exam_detail_count = count($db->result());
    $query = "select start_date, end_date from sh_exams where id = ".$exam_id;
    $db->query($query);
    $result = $db->row();
    $start_session = $result['start_date'];
    $end_session = $result['end_date'];
    if($exam_detail_count > 0){
        $xcrud->set_exception('subject_id',$ci->lang->line('exam_already'));
    }
    else if($exam_date < $start_session || $end_session < $exam_date){
        $xcrud->set_exception('exam_date',$ci->lang->line('exam_out'));
    }
    else if ($start_time == $end_time){
        $xcrud->set_exception('start_time,end_time',$ci->lang->line('start_time_validation'));
    }
    else if($end_time < $start_time){
        $xcrud->set_exception('start_time,end_time',$ci->lang->line('end_time_validation'));
    }
    else if($total_marks < 0){
        $xcrud->set_exception('total_marks',$ci->lang->line('total_negative'));
    }
    else if($passing_marks < 0){
        $xcrud->set_exception('passing_marks',$ci->lang->line('pasing_negative'));
    }
    else if($total_marks < $passing_marks){
        $xcrud->set_exception('passing_marks',$ci->lang->line('passing_greater'));
    }
    else{
        $query = "select count(case when CAST('$start_time' AS time) < end_time and start_time < CAST('$end_time' AS time) and exam_date = '$exam_date' then 1 end) as overlap from sh_exam_details where exam_id=$exam_id and subject_id in (select id from subjects where 1) and deleted_at is null";
        $db->query($query);
        $overlap = $db->row()['overlap'];
        if($overlap > 0){
            $xcrud->set_exception('exam_date,start_time,end_time',$ci->lang->line('exam_overlapping'));
        }
    }
}

function check_exam_details_update($postdata, $primary, $xcrud) {
    //$session_id = $postdata->get('session_id')!==null?$postdata->get('session_id'):'null';
    $exams = explode(",",$postdata->get('exam_id'));
    $class_id = $postdata->get('class_id');
    $batches = explode(",",$postdata->get('batch_id'));
    $subject_group_id = $postdata->get('subject_group_id');
    $subjects = explode(",",$postdata->get('subject_id'));
    //$type = $postdata->get('type');
    //$total_marks = $postdata->get('total_marks');
    //$passing_marks = $postdata->get('passing_marks');

    $db = Xcrud_db::get_instance();
    foreach($exams as $exam_id){
        $sql = "SELECT passing_marks, total_marks FROM sh_exams WHERE id='$exam_id' ";
        $db->query($sql);
        $result = $db->result();
        $passing_marks = 0;
        $total_marks = 0;
        if(count($result) > 0){
            $passing_marks = $result[0]["passing_marks"];
            $total_marks = $result[0]["total_marks"];
        }
        foreach($batches as $bth_id){
            foreach($subjects as $sub_id){
                $sql = "REPLACE INTO sh_exam_details (exam_id, class_id, batch_id, subject_id, 
                    total_marks, passing_marks, subject_group_id) 
                VALUES ($exam_id, $class_id, $bth_id, $sub_id,$total_marks, $passing_marks, $subject_group_id)";
                $db->query($sql);    
            }    
        }
    }
    
    /*$exam_detail_count = count($db->result());
    $query = "select start_date, end_date from sh_exams where id = ".$exam_id;
    $db->query($query);
    $result = $db->row();
    $start_session = $result['start_date'];
    $end_session = $result['end_date'];
    if($exam_date < $start_session || $end_session < $exam_date){
        $xcrud->set_exception('exam_date','Exam date is out of exam session period');
    }
    else if ($start_time == $end_time){
        $xcrud->set_exception('start_time,end_time','Start time and end time cannot be same.');
    }
    else if($end_time < $start_time){
        $xcrud->set_exception('start_time,end_time','End time must be greater than start time.');
    }
    
    if($total_marks < 0){
        $xcrud->set_exception('total_marks','Total marks can not be negative');
    }
    else if($passing_marks < 0){
        $xcrud->set_exception('passing_marks','Passing marks can not be negative');
    }
    else if($total_marks < $passing_marks){
        $xcrud->set_exception('passing_marks','Passing marks can not be greater than total marks');
    }
    else{
        $query = "select count(case when CAST('$start_time' AS time) < end_time and start_time < CAST('$end_time' AS time) and exam_date = '$exam_date' then 1 end) as overlap from sh_exam_details where exam_id=$exam_id and subject_id in (select subjects from sh_subject_groups where class_id=$class_id and FIND_IN_SET($batch_id,batch_id) and deleted_at is null) and deleted_at is null and id <> $primary AND class_id=$class_id AND batch_id=$batch_id";
        $db->query($query);
        $overlap = $db->row()['overlap'];
        if($overlap > 0){
            $xcrud->set_exception('exam_date,start_time,end_time','Exam date and time is overlapping with another subject of this section');
        }
    }*/
}

/*function passing_rules_insert($postdata, $xcrud) {
    $subjects_which_passed = explode(",",$postdata->get('subjects_which_passed'));
    foreach($subjects_which_passed as $sub){
        $exam_id = $postdata->get('exam_id');
        $class_id = $postdata->get('class_id');
        $subject_group_id = $postdata->get('subject_group_id');
        $percentage = $postdata->get('minimum_percentage');

        if($percentage < 0 || $percentage > 100){
            $xcrud->set_exception('minimum_percentage', 'Minimum Percentage should be between 0-100');
        }else{
            $db = Xcrud_db::get_instance();
            $query = "SELECT id FROM sh_passing_rules WHERE exam_id = $exam_id AND class_id = $class_id AND subject_group_id='$subject_group_id' AND subjects_which_passed='$sub' AND deleted_at IS NULL "; 
            $db->query($query);
            $result = $db->result();
            $count = count($result);
            if($count > 0 ){
                $xcrud->set_exception('class_id', 'Passing rules already added for this class.');
            }
        }
    }
}*/

/*function replace_passing_rules_insert($postdata, $xcrud){
    $subjects_which_passed = explode(",",$postdata->get('subjects_which_passed'));
    $exam_id = $postdata->get('exam_id');
    $class_id = $postdata->get('class_id');
    $subject_group_id = $postdata->get('subject_group_id');
    $operator = $postdata->get('operator');
    $minimum_percentage = $postdata->get('minimum_percentage');
    $values = "";
    foreach($subjects_which_passed as $sub){
        $values .= "(".$exam_id.",".$class_id.",".$minimum_percentage.",'".$operator."',".$sub.",".$subject_group_id."),";  
    }
    $db = Xcrud_db::get_instance();
    $query = "INSERT INTO `sh_passing_rules`(`exam_id`, `class_id`, `minimum_percentage`, `operator`, `subjects_which_passed`, `subject_group_id`) VALUES ". rtrim($values,",");
    $db->query($query);
    return 1;
}*/

function replace_passing_rules_update($postdata, $primary ,$xcrud){
    $subjects_which_passed = explode(",",$postdata->get('subjects_which_passed'));
    $exam_id = $postdata->get('exam_id');
    $class_id = $postdata->get('class_id');
    $subject_group_id = $postdata->get('subject_group_id');
    $minimum_percentage = $postdata->get('minimum_percentage');
    $operator = $postdata->get('operator');
    $db = Xcrud_db::get_instance();

    foreach($subjects_which_passed as $sub){
        $query = "SELECT id FROM sh_passing_rules WHERE exam_id = $exam_id AND class_id = $class_id AND subject_group_id='$subject_group_id' AND subjects_which_passed='$sub' AND deleted_at IS NULL"; 
        $db->query($query);
        $result = $db->result();
        $count = count($result);
        if($count == 0 ){
            $query = "INSERT INTO `sh_passing_rules`(`exam_id`, `class_id`, `minimum_percentage`, `operator`, `subjects_which_passed`, `subject_group_id`) VALUES (".$exam_id.",".$class_id.",".$minimum_percentage.",'".$operator."',".$sub.",".$subject_group_id.")";
            $db->query($query);
        } else {
            $query = "UPDATE sh_passing_rules SET exam_id='$exam_id', class_id='$class_id', minimum_percentage='$minimum_percentage', operator='$operator', subjects_which_passed='$sub', subject_group_id='$subject_group_id' WHERE id='$primary'";
            $db->query($query);
        }
    }
}

function custom_exam_details($postdata, $xcrud){
    $batches = explode(",",$postdata->get('batch_id'));
    $subjects = explode(",",$postdata->get('subject_id'));
    $exams = explode(",",$postdata->get('exam_id'));
    $values = "";
    $db = Xcrud_db::get_instance();
    foreach($exams as $exam_id){
        $sql = "SELECT passing_marks, total_marks FROM sh_exams WHERE id='$exam_id' ";
        $db->query($sql);
        $result = $db->result();
        $passing_marks = 0;
        $total_marks = 0;
        if(count($result) > 0){
            $passing_marks = $result[0]["passing_marks"];
            $total_marks = $result[0]["total_marks"];
        }
        foreach($batches as $bth){
            foreach($subjects as $sub){
                $values .= "(".$exam_id.",".$postdata->get('class_id').",".$bth.",".$sub.",".$total_marks.",".$passing_marks.",".$postdata->get('subject_group_id')."),";
            }
        }
    }
    
    
    $query = "INSERT INTO `sh_exam_details`(`exam_id`, `class_id`, `batch_id`, `subject_id`, `total_marks`, `passing_marks`, `subject_group_id`) VALUES ". rtrim($values,",");
    $db->query($query);
    return 1;
}

function insert_result_card_complete_settings($postdata, $xcrud){
    print_r($postdata->to_array()); die();
    print_r($postdata->get('exam_result_card_id'));
    print_r($postdata->get('last_exam_group_id')); die();
    $db = Xcrud_db::get_instance();
    $query = "INSERT INTO `sh_complete_result_card_settings`(`name`, `exam_result_card_id`, `class_id`, `batch_id`, `session_id`, `last_exam_group_id`) VALUES ('". $postdata->get('name')."',".$postdata->get('exam_result_card_id').",". $postdata->get('class_id').",".$postdata->get('batch_id').",".$postdata->get('session_id').",".$postdata->get('last_exam_group_id'). ")";
    $db->query($query);
}

function upldate_complete_result_card_settings($postdata, $primary ,$xcrud){
    
}

function reloadPage($primary, $xcrud) {
    echo "<script type='text/javascript'>";
    echo "location.reload();";
    echo "</script>";
}

function save_skill_and_assessment_groups($postdata, $xcrud){
    $classes = explode(",",$postdata->get('class_id'));
    $assessment = $postdata->get('assessment');
    $code = $postdata->get('code');
    $values = "";
    foreach($classes as $cls){
        $values .= "('".$assessment."','".$code."',".$cls."),";
    }
    $db = Xcrud_db::get_instance();
    $query = "REPLACE INTO sh_skill_and_assessment_groups(assessment, code, class_id) VALUES ".rtrim($values,',');
    $db->query($query);
    return 1;
}

function update_skill_and_assessment_groups ($postdata, $primary ,$xcrud) {

}

function concate_items($value, $fieldname, $primary, $row, $xcrud){
    $db = Xcrud_db::get_instance();
    $values = explode(",",$value);
    $newValues = "";
    foreach($values as $val){
        $vald = "";
        $query = "SELECT item_store FROM school_store WHERE id='$val' ";
        $db->query($query);
        //$vald = "<span class='badge badge-info' style='margin-left: 2px;'>".$db->row()["item_store"]."</span>";
        $vald = $db->row()["item_store"].",";
        $newValues .= $vald;
    }
    return rtrim($newValues,",");
}

function quantities_callback($value, $fieldname, $primary, $row, $xcrud){
    $values = explode(",",$value);
    $newValues = "";
    foreach($values as $val){
        $newValues .= $val.",";
    }
    return rtrim($newValues,",");
}

function currency_callback($value, $fieldname, $primary, $row, $xcrud){
    $CI = &get_instance();
    return $CI->session->userdata("student")["currency_symbol"].$value;
}

function currency_callback2($value, $fieldname, $primary, $row, $xcrud){
    $CI = &get_instance();
    return $CI->session->userdata("admin")["currency_symbol"].$value;
}

function image_callback($value, $fieldname, $primary_key, $row, $xcrud) {
    $url = base_url()."uploads/schoolstore/".$value;
    return "<img src='$url' width='40px' height='40px'/>";
}

function update_stock($postdata, $primary, $xcrud) {
    $db = Xcrud_db::get_instance();
    $is_collected = $postdata->get("is_collected");
    $item_id = $postdata->get("item_id");
    $order_id = $postdata->get("order_id");
    $query = "SELECT is_collected FROM order_details WHERE id=$primary";
    $db->query($query);
    $orderInfo = $db->row();
    if($orderInfo["is_collected"] == 'No') {
        if($is_collected == 'Yes'){
            $query = "SELECT stock FROM school_store WHERE id='$item_id' ";
            $db->query($query);
            $stock = $db->row()["stock"];
            if($stock>0) {
                $updatedStock = $stock - $postdata->get("quantity");
                $query = "UPDATE school_store SET stock='$updatedStock' WHERE id='$item_id'";
                $db->query($query);
            }
        }
    } else {
        $xcrud->set_exception('', 'Order already collected', 'error');
    }
}

function custom_order_details_insert($postdata, $xcrud){
    echo $postdata->get("item_id");
    echo $postdata->get("is_collected");
    echo $postdata->get("collected_by");
    echo $postdata->get("quantity");
    echo $postdata->get("updated_at");
}

function update_stock2($postdata, $primary, $xcrud) {
    print_r($postdata->all());
    /*$db = Xcrud_db::get_instance();
    $is_collected = $postdata->get("is_collected");
    $item_id = $postdata->get("item_id");
    $order_id = $postdata->get("order_id");
    $query = "SELECT is_collected FROM order_details WHERE id=$primary";
    $db->query($query);
    $orderInfo = $db->row();
    if($orderInfo["is_collected"] == 'No') {
        if($is_collected == 'Yes'){
            $query = "SELECT stock FROM school_store WHERE id='$item_id' ";
            $db->query($query);
            $stock = $db->row()["stock"];
            if($stock>0) {
                $updatedStock = $stock - $postdata->get("quantity");
                $query = "UPDATE school_store SET stock='$updatedStock' WHERE id='$item_id'";
                $db->query($query);
            }
        }
    } else {
        $xcrud->set_exception('', 'Order already collected', 'error');
    }*/
}

function update_order_status($postdata, $primary, $xcrud){
    $db = Xcrud_db::get_instance();
    $order_id = $postdata->get("order_id");
    $query = "SELECT * FROM order_details WHERE order_id='$order_id' ";
    $db->query($query);
    $result = $db->result();
    $status = "Not-Collected";
    $arr = array();
    foreach($result as $res){
        if($res["is_collected"] == "Yes"){
            $arr[] = "Collected";
        } else if($res["is_collected"] == 'No'){
            $arr[] = "Not-Collected";
        }
    }
    $arr = array_unique($arr);
    if(in_array("Collected", $arr) && count($arr) == 1){
        $status = "Collected";
    } else if(in_array("Not-Collected", $arr) && count($arr) == 1){
        $status = "Not-Collected";
    } else if(in_array("Collected", $arr) && in_array("Not-Collected", $arr)){
        $status = "Partial";
    }
    $query = "UPDATE orders SET status='$status' WHERE id='$order_id'";
    $db->query($query);
}

function approvals_by_callback($value, $fieldname, $primary, $row, $xcrud) {
    if($value == 0){
        return "0/5";
    } else {
        return $value."/5";
    }
}

function proper_number_format($value, $fieldname, $primary, $row, $xcrud){
    return number_format($value, 2, '.', ',');
}

function proper_number_format_with_dr_or_cr($value, $fieldname, $primary, $row, $xcrud){
    $db = Xcrud_db::get_instance();
    $query = "SELECT opening_balance_type FROM y_accounts WHERE id='$primary'";
    $db->query($query);
    $res = $db->result();
    if(count($res) > 0){
        if($value != 0){
            if($res[0]["opening_balance_type"] == "debit"){
                return number_format($value, 2, '.', ',') . " (Dr)";
            } else if($res[0]["opening_balance_type"] == "credit"){
                return number_format($value, 2, '.', ',') . " (Cr)";
            }
        } else if ($value == 0){
            return number_format($value, 2, '.', ',');
        }
    }
}

function delete_journal_voucher($primary, $xcrud){
    $db = Xcrud_db::get_instance();
    $db->query("UPDATE y_journal_voucher SET deleted_at='".date('Y-m-d h:i:s')."' WHERE id='$primary'");
    $db->query("UPDATE y_transctions SET deleted_at='". date('Y-m-d h:i:s') ."' WHERE y_journal_voucher_id='$primary'");
}