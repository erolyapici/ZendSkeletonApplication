<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Album\Controller;

use Album\Model\Album;
use Zend\Form\Annotation\Input;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AlbumController extends AbstractActionController
{
    protected $albumTable;
    public function indexAction()
    {
        return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form = new Form('album');
        $isPost = $request->isPost();
        if($isPost){

            $postData = $request->getPost();
            foreach($postData AS $name=>$val){
                $form->add(array('name'=>$name));
            }
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($postData);


            if($form->isValid()){
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);

                return $this->redirect()->toRoute('album');
            }
        }

        return array('form' => $form, 'isPost' => $isPost);
    }

    public function editAction()
    {

        $id = (int)$this->params()->fromRoute('id',0);

        if(!$id){
            return $this->redirect()->toRoute('album',array(
                'action' => 'add'
            ));
        }

        try{

            $album = $this->getAlbumTable()->getAlbum($id);

        }catch (\Exception $ex){die("asfd$id");
            return $this->redirect()->toRoute('album',array(
                'action' => 'index'
            ));
        }

        $form = new Form();
        $request = $this->getRequest();
        $isPost = $request->isPost();
        if($isPost){
            $postData = $request->getPost();
            foreach($postData AS $name=>$val){
                $form->add(array('name'=>$name));
            }

            $form->setInputFilter($album->getInputFilter());
            $form->setData($postData);

            if($form->isValid()){
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);

                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'isPost' => $isPost,
            'data' => $album,
        );

    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id',0);
        if(!$id){
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if($request->isPost()){
            $del = $request->getPost('del','No');

            if($del == 'Yes'){
                $id = (int)$request->getPost('id');
                $this->getAlbumTable()->deleteAlbum($id);
            }
            return $this->redirect()->toRoute('album');
        }
        return array(
            'id'    => $id,
            'album' => $this->getAlbumTable()->getAlbum($id)
        );

    }

    public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
}
