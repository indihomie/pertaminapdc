<?php

/*
*  Build on pojay.dev @42A
*/

/**
 * Description of class
 *
 * @author mazte
 */
class UIHelper
{

  /**
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $type <strong>Type of Input<strong>default is text<p>"hidden" "text"</p>
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */

  function createInput($val, $id, $name, $class = "", $type = "text")
  {
    return '<input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" />';
  }


  function createField($label, $id, $value, $required = "", $fieldTable = "", $attr = "", $addEvent = "", $maxlength = "", $readonly = "", $datePicker = "", $classLabel = "")
  {
    $ret = '         
      <label class="l-input-small' . (!empty($fieldTable) ? '2' : $classLabel) . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*)</span></label>' : '</label>') . '
      <div class="field">           
          <input type="text" id="' . $id . '" name="' . $id . '" ' . $addEvent . ' value="' . $value . '" class="mediuminput ' . (!empty($datePicker) ? 'hasDatePicker' : '') . '"" ' . $attr . ' maxlength="' . $maxlength . '" ' . (!empty($readonly) ? 'readonly' : '') . ' /> 
      </div>';
    return $ret;
  }

  function createFieldWithoutLabel($id, $value, $attr = "", $addEvent = "", $maxlength = "", $readonly = "")
  {
    $ret = '                    
      <input type="text" id="' . $id . '" name="' . $id . '" ' . $addEvent . ' value="' . $value . '" class="mediuminput" ' . $attr . ' maxlength="' . $maxlength . '" ' . (!empty($readonly) ? 'readonly' : '') . ' /> ';
    return $ret;
  }

  function createTimePicker($label, $id, $value, $required = "", $fieldTable = "", $addEvent = "")
  {
    $ret = '         
      <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*)</span></label>' : '</label>') . '
      <div class="field">           
          <input type="text" id="' . $id . '" name="inp[' . $id . ']" ' . $addEvent . ' value="' . $value . '" class="vsmallinput hasTimePicker" style="background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;" readonly="readonly" size="10" maxlength="5" /> 
      </div>';
    return $ret;
  }

  function createComboData($label, $sql, $key, $value, $id, $selectedValue, $addEvent = "", $width = "", $classChosen = "", $required = "", $fieldTable = "", $all = " ", $classLabel = "")
  {
    $ret = '';
    $ret .= '
    <label class="l-input-small' . (!empty($fieldTable) ? '2' : $classLabel) . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*)</span></label>' : '</label>') . '           
    <div class="field">
        ' . comboData($sql, $key, $value, $id, $all, $selectedValue, $addEvent, (!empty($width) ? $width : '250px'), (!empty($classChosen) ? 'chosen-select' : '')) . '
    </div>';

    return $ret;
  }

  function createRadio($label, $name, $arrayValue, $selectedValue, $fieldTable = "", $addEvent = "", $required = "")
  {
    $ret = '';
    $ret .= '
      <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '">' . $label . ' ' . (!empty($required) ? '<span class="required">*)</span></label>' : '</label>') . '
      <div class="field">';
    $count = 0;
    foreach ($arrayValue as $key => $value) {
      if ($count == 0 && $selectedValue == "") {
        $tchecked = "checked=\"checked\"";
      } else {
        $tchecked = ($key == $selectedValue ? " checked = \"checked\"" : "");
      }
      $ret .= "<input type='radio' name='$name' $addEvent value='$key' $tchecked /> <span class='sradio'>$value</span>";
      $count++;
    }
    $ret .= "
      </div>";

    return $ret;
  }

  function createSingleCheckBox($label, $id, $value, $selectedValue, $text = "", $fieldTable = "", $addEvent = "", $required = "")
  {
    $ret = '';
    $checked = ($selectedValue == $value ? " checked = \"checked\"" : "");
    $ret .= '
    <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '">' . $label . ' ' . (!empty($required) ? '<span class="required">*)</span></label>' : '</label>') . '
      <div class="field">
      <input type="checkbox" id="' . $id . '" name="' . $id . '" value="' . $value . '" ' . $checked . ' ' . $addEvent . ' /> ' . $text . '
    </div>';

    return $ret;
  }


  function createTextArea($label, $id, $value, $attr = "", $fieldTable = "", $required = "", $addEvent = "", $classLabel = "")
  {
    $ret = '';
    $ret .= '
  <label class="l-input-small' . (!empty($fieldTable) ? '2' : $classLabel) . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*)</span></label>' : '') . '
  <div class="field">
      <textarea name="' . $id . '" id="' . $id . '" class="longinput" ' . (empty($attr) ? 'style="width:250px;"' : $attr) . ' ' . $addEvent . '>' . $value . '</textarea>
  </div>';

    return $ret;
  }

  function createFile($label, $id, $value, $attr = "", $fieldTable = "", $target, $idTarget, $delFunction, $classLabel = "")
  {
    global $par;
    $ret = '';
    $ret .= '  
    <label class="l-input-small' . (!empty($fieldTable) ? '2' : $classLabel) . '" >' . $label . '</label>
    <div class="field">';
    empty($value) ?
      $ret .= '
    <input type="text" id="fileTemp" name="fileTemp" class="input" ' . (empty($attr) ? 'style="width:180px;"' : $attr) . '/>
    <div class="fakeupload" ' . (empty($attr) ? 'style="width:350px;"' : $attr) . '>
      <input type="file" id="' . $id . '" name="' . $id . '" class="realupload" size="50" onchange="this.form.fileTemp.value = this.value;" />
    </div>
    ' : $ret .= '
    <a href="download.php?d=' . $target . '&f=' . $idTarget . '"><img src="' . getIcon($value) . '" align="left"></a>
    <input type="file" id="' . $id . '" name="' . $id . '" style="display:none;" />
    <a href="?par[mode]=' . $delFunction . getPar($par, "mode") . '" onclick="return confirm(\'anda yakin akan menghapus file ini?\')" class="action delete"><span>Delete</span></a>
    <br clear="all"/>';
    $ret .= '
    </div>';

    return $ret;
  }

  function createSpan($label, $value, $id = "", $fieldTable = "")
  {
    $ret = '
  <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '" >' . $label . '</label>
  <span class="field" id="' . $id . '">' . $value . '&nbsp;</span>';

    return $ret;
  }

  function createDashboardBox($label, $color = "", $value, $attr = "", $class = "")
  {
    $arrColor = array("dark-orchid", "camarone", "goldenrod", "citrus", "caper", "moon-raker", "allports", "chocolate");
    $randColor = array_rand($arrColor);
    $color = empty($color) ? $arrColor[$randColor] : $color;
    $class = empty($class) ? "box" : $class;
    $ret = '
  <div class="' . $class . ' ' . $color . '" ' . $attr . '>
      <div class="' . $class . '-header">
          <p class="' . $class . '-title">' . $label . '</p>
      </div>
      <div class="' . $class . '-content">
          <p class="' . $class . '-number">' . $value . '</p>
      </div>
  </div>';

    return $ret;
  }

  function createDatePicker($label, $id, $value, $required = "", $fieldTable = "", $addEvent = "", $sideAttr = "", $classLabel = "")
  {
    $ret = '         
      <label class="l-input-small' . (!empty($fieldTable) ? '2' : $classLabel) . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*</span></label>' : '</label>') . '
      <div class="field">           
          <input type="text" id="' . $id . '" name="' . $id . '" ' . $addEvent . ' value="' . getTanggal($value) . '" class="mediuminput hasDatePicker" /> ' . $sideAttr . '
      </div>';
    return $ret;
  }
}
