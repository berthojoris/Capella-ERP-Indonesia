<?php
$this->breadcrumbs=array(
	'Accounts',
);
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']);
?>
<script type="text/javascript">
function adddata()
{jQuery.ajax({'url':'/index.php?r=account/create','data':$(this).serialize(),'type':'post','dataType':'json','success':function(data)
{document.getElementById('messages').innerHTML='';if(data.status=='success')
{$('#createdialog div.divcreate').html(data.div);$('#Account_accountid').val('');
$('#Account_accountname').val('');$('#Account_accountcode').val('');
$('#Account_parentaccountid').val('');$('#parentaccountname').val('');
$('#Account_accounttypeid').val('');$('#accounttypename').val('');
$('#Account_currencyid').val('');$('#currencyname').val('');
$('#createdialog').dialog('open');}
else
{document.getElementById('messages').innerHTML = data.div;}},'cache':false});;return false;}
</script>
<script type="text/javascript">
function editdata()
{jQuery.ajax({'url':'/index.php?r=account/update','data':{'id':$.fn.yiiGridView.getSelection("datagrid")},'type':'post','dataType':'json','success':function(data)
{document.getElementById('messages').innerHTML='';if(data.status=='success')
{$('#createdialog div.divcreate').html(data.div);$('#Account_accountid').val(data.accountid);
$('#Account_accounttypeid').val(data.accounttypeid);$('#accounttypename').val(data.accounttypename);
$('#Account_accountname').val(data.accountname);$('#Account_accountcode').val(data.accountcode);$('#Account_parentaccountid').val(data.parentaccountid);$('#parentaccountname').val(data.parentaccountname);$('#Account_currencyid').val(data.currencyid);$('#currencyname').val(data.currencyname);if(data.recordstatus=='1')
{document.forms[1].elements[15].checked=true;}
else
{document.forms[1].elements[15].checked=false;}
$('#createdialog').dialog('open');}
else
{document.getElementById('messages').innerHTML = data.div;}},'cache':false});;return false;}
</script>
<script type="text/javascript">
function deletedata()
{jQuery.ajax({'url':'/index.php?r=account/delete','data':{'id':$.fn.yiiGridView.getSelection("datagrid")},'type':'post','dataType':'json','success':function(data)
{},'cache':false});;$.fn.yiiGridView.update('datagrid');return false;}
</script>
<script type="text/javascript">
function refreshdata()
{$.fn.yiiGridView.update('datagrid');return false;}
</script>
<script type="text/javascript">
function helpdata(value) {
	jQuery.ajax({
        'url': '/index.php?r=account/help',
        'data': {
            'id': value
        },
        'type': 'post',
        'dataType': 'json',
        'success': function(data) {
            if (data.status == 'success') {
				document.getElementById('divhelp').innerHTML = data.div;
                $('#helpdialog').dialog('open');
            } else {
                document.getElementById('messages').innerHTML = data.div;
            }
        },
        'cache': false
    });;
    return false;
}
</script>
<script type="text/javascript" >
	$(function(){
		var btnUpload=$('#upload');
		var status=$('#messages');
		new AjaxUpload(btnUpload, {
			action: 'index.php?r=account/upload',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				 if (!(ext && /^(csv)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('Only CSV files are allowed');
					return false;
				}
				status.text('Uploading...');
			},
			onComplete: function(file, response){
				status.text('');
				//Add uploaded file to list
				if(response==='success'){
					$.fn.yiiGridView.update('datagrid');
				} else{
					status.text(response);
				}
			}
		});		
	});
</script>
<script type="text/javascript">
function downloaddata() {
	window.open('/index.php?r=account/download&id='+$.fn.yiiGridView.getSelection("datagrid"));
}
</script>
<?php
$this->widget('application.extensions.tipsy.Tipsy', array(
  'fade' => false,
  'gravity' => 'n',
  'items' => array(
    array('id' => '#accounttypename'
     ,'fallback' => Catalogsys::model()->getcatalog('enterpopaccounttype'),'html'=>true),    
    array('id' => '#parentaccountname'
     ,'fallback' => Catalogsys::model()->getcatalog('enterpopaccount'),'html'=>true),    
    array('id' => '#currencyname'
     ,'fallback' => Catalogsys::model()->getcatalog('enterpopcurrencyname'),'html'=>true),    
    array('id' => array('model' => $model, 'attribute' => 'accountname')
     ,'fallback' => Catalogsys::model()->getcatalog('enteraccountname'),'html'=>true),    
    array('id' => array('model' => $model, 'attribute' => 'accountcode')
     ,'fallback' => Catalogsys::model()->getcatalog('enteraccountcode'),'html'=>true),    
    array('id' => array('model' => $model, 'attribute' => 'parentaccountid')
     ,'fallback' => Catalogsys::model()->getcatalog('enterparentaccountid'),'html'=>true),    
    array('id' => array('model' => $model, 'attribute' => 'accounttypeid')
     ,'fallback' => Catalogsys::model()->getcatalog('enteraccounttypeid'),'html'=>true),    
    array('id' => array('model' => $model, 'attribute' => 'currencyid')
     ,'fallback' => Catalogsys::model()->getcatalog('entercurrencyid'),'html'=>true),    
    array('id' => array('model' => $model, 'attribute' => 'recordstatus')
     ,'fallback' => Catalogsys::model()->getcatalog('enterrecordstatus'),'html'=>true),    
  ),  
));
?>
<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array( // the dialog
    'id'=>'createdialog',
    'options'=>array(
        'title'=>'Form Dialog',
        'autoOpen'=>false,
        'modal'=>true,
        'width'=>'auto',
        'height'=>'auto',
    ),
));?>
<div id="divcreate"></div>
<?php echo $this->renderPartial('_form', array('model'=>$model,
				  'parentaccount'=>$parentaccount,
				  'currency'=>$currency,
    'accounttype'=>$accounttype)); ?>
<?php $this->endWidget();?>
<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array( // the dialog
    'id'=>'helpdialog',
    'options'=>array(
        'title'=>'Help',
        'autoOpen'=>false,
        'modal'=>true,
        'width'=>'auto',
        'height'=>'auto',
    ),
));?>
<div id="divhelp"></div>
<?php $this->endWidget();?>
<h1><?php echo Catalogsys::model()->GetCatalog('account') ?></h1>
<?php
$this->widget('ToolbarButton',array('isCreate'=>true,'isEdit'=>true,'isDelete'=>true,
	'isUpload'=>true,'UrlUpload'=>'index.php?r=account/upload',
	'isDownload'=>true,'isRefresh'=>true,
	'isHelp'=>true,'OnClick'=>"{helpdata(1)}",
	'isRecordPage'=>true,'PageSize'=>$pageSize,'OnChange'=>"$.fn.yiiGridView.update('datagrid',{data:{pageSize: $(this).val() }})"));
?>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'datagrid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'selectableRows'=>1,
		'afterAjaxUpdate' => 'function(id,data){ initTipsy(); }',
	'template'=>'{pager}<br>{items}{pager}',
	'columns'=>array(
    array(
      'class'=>'CCheckBoxColumn',
      'id'=>'ids',
    ),
	array('name'=>'accountid','visible'=>false, 'value'=>'$data->accountid','htmlOptions'=>array('width'=>'1%')),
	array('name'=>'accounttypeid', 'value'=>'($data->accounttype!==null)?$data->accounttype->accounttypename:""'),
	array('name'=>'accountcode', 'value'=>'$data->accountcode','htmlOptions'=>array('width'=>'30%')),
	array('name'=>'accountname', 'value'=>'$data->accountname','htmlOptions'=>array('width'=>'30%')),
	array('name'=>'parentaccountid', 'value'=>'($data->parentaccount!==null)?$data->parentaccount->accountcode:""'),
	array('name'=>'currencyid', 'value'=>'($data->currency!==null)?$data->currency->currencyname:""'),
    array(
      'class'=>'CCheckBoxColumn',
      'name'=>'recordstatus',
      'selectableRows'=>'0',
      'header'=>'Record Status',
      'checked'=>'$data->recordstatus',
    ),
  ),
));
?>
