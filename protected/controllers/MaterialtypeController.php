<?php

class MaterialtypeController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column3';
protected $menuname = 'materialtype';

	public function actionHelp()
	{
		if (isset($_POST['id'])) {
			$id= (int)$_POST['id'];
			switch ($id) {
				case 1 : $this->txt = '_help'; break;
				case 2 : $this->txt = '_helpmodif'; break;
			}
		}
	  parent::actionHelp();
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
	  parent::actionCreate();
		$model=new Materialtype;

		if (Yii::app()->request->isAjaxRequest)
        {
            echo CJSON::encode(array(
                'status'=>'success',
                'divcreate'=>$this->renderPartial('_form', array('model'=>$model), true)
				));
            Yii::app()->end();
        }
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
	  parent::actionUpdate();
		$id=$_POST['id'];
	  $model=$this->loadModel($id[0]);
if ($model != null)
      {
        if ($this->CheckDataLock($this->menuname, $id[0]) == false)
        {
          $this->InsertLock($this->menuname, $id[0]);
            echo CJSON::encode(array(
                'status'=>'success',
				'materialtypeid'=>$model->materialtypeid,
				'materialtypecode'=>$model->materialtypecode,
				'description'=>$model->description,
				'recordstatus'=>$model->recordstatus,
                'div'=>$this->renderPartial('_form', array('model'=>$model), true)
				));
            Yii::app()->end();
        }
        }
	}

     public function actionCancelWrite()
    {
      $this->DeleteLockCloseForm($this->menuname, $_POST['Materialtype'], $_POST['Materialtype']['materialtypeid']);
    }

	public function actionWrite()
	{
	  parent::actionWrite();
	  if(isset($_POST['Materialtype']))
	  {
        $messages = $this->ValidateData(
                array(array($_POST['Materialtype']['materialtypecode'],'emptymaterialtypecode','emptystring'),
                array($_POST['Materialtype']['description'],'emptydescription','emptystring'),
            )
        );
        if ($messages == '') {
		//$dataku->attributes=$_POST['Materialtype'];
		if ((int)$_POST['Materialtype']['materialtypeid'] > 0)
		{
		  $model=$this->loadModel($_POST['Materialtype']['materialtypeid']);
		  $model->materialtypecode = $_POST['Materialtype']['materialtypecode'];
		  $model->description = $_POST['Materialtype']['description'];
		  $model->recordstatus = $_POST['Materialtype']['recordstatus'];
		}
		else
		{
		  $model = new Materialtype();
		  $model->attributes=$_POST['Materialtype'];
		}
		try
          {
            if($model->save())
            {
              $this->DeleteLock($this->menuname, $_POST['Materialtype']['materialtypeid']);
              $this->GetSMessage('pmmtinsertsuccess');
            }
            else
            {
              $this->GetMessage($model->getErrors());
            }
          }
          catch (Exception $e)
          {
            $this->GetMessage($e->getMessage());
          }
        }
	  }
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete()
	{
	  parent::actionDelete();
		$id=$_POST['id'];
		foreach($id as $ids)
		{
		  $model=$this->loadModel($ids);
		  $model->recordstatus=0;
		  $model->save();
		}
		echo CJSON::encode(array(
                'status'=>'success',
                'div'=>'Data deleted'
				));
        Yii::app()->end();
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
	  parent::actionIndex();
		$model=new Materialtype('search');
	  $model->unsetAttributes();  // clear any default values
	  if(isset($_GET['Materialtype']))
			$model->attributes=$_GET['Materialtype'];
	  if (isset($_GET['pageSize']))
	  {
		Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
		unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
	  }
	  $this->render('index',array(
		'model'=>$model
	  ));
	}
	
	public function actionDownload()
  {
	parent::actionDownload();
    $pdf = new PDF();
    $pdf->title='Material Type List';
    $pdf->AddPage('P');
    $pdf->setFont('Arial','B',12);

    // definisi font
    $pdf->setFont('Arial','B',8);

    // menuliskan tabel
    $connection=Yii::app()->db;
    $sql = "select a.materialtypecode,a.description
      from materialtype a";
    $command=$connection->createCommand($sql);
    $dataReader=$command->queryAll();

    $pdf->setaligns(array('C','C'));
    $pdf->setwidths(array(50,70));
    $pdf->Row(array('Material Type Code','Description'));
    $pdf->setaligns(array('L','L'));
    foreach($dataReader as $row1)
    {
      $pdf->row(array($row1['materialtypecode'],$row1['description']));
    }
    // me-render ke browser
    $pdf->Output('materialtype.pdf','D');
  }

  public function actionUpload()
	{
      parent::actionUpload();
	  $folder=$_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/upload/';// folder for uploaded files
	  $allowedExtensions = array("csv");
	  $sizeLimit = (int)Yii::app()->params['sizeLimit'];// maximum file size in bytes
	  $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
	  $result = $uploader->handleUpload($folder,true);
	  $row = 0;
	  if (($handle = fopen($folder.$uploader->file->getName(), "r")) !== FALSE) {
		  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($row>0) {
			  $model=Materialtype::model()->findByPk((int)$data[0]);
			  if ($model=== null) {
				$model = new Materialtype();
			  }
			  $model->materialtypeid = (int)$data[0];
			  $model->materialtypecode = $data[1];
			  $model->description = $data[2];
			  $model->recordstatus = (int)$data[3];
			  try
			  {
				if(!$model->save())
				{
				  $errormessage=$model->getErrors();
				  if (Yii::app()->request->isAjaxRequest)
				  {
					echo CJSON::encode(array(
					  'status'=>'failure',
					  'div'=>$errormessage
					));
				  }
				}
			  }
			  catch (Exception $e)
			  {
				$errormessage=$e->getMessage();
				if (Yii::app()->request->isAjaxRequest)
				  {
					echo CJSON::encode(array(
					  'status'=>'failure',
					  'div'=>$errormessage
					));
				  }
			  }
			}
			$row++;
		  }
		  fclose($handle);
	  }
	  $result=htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	  echo $result;
  }

	/**
	 *
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Materialtype::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='materialtype-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
