<?php
/**
<<<<<<< HEAD
=======
 * Created by PhpStorm.
 * User: execut
 * Date: 6/28/17
 * Time: 1:46 PM
>>>>>>> b22cdec76cc1977898ebf47f4cd435e17e89643a
 */

namespace execut\crudFields;


abstract class Plugin
{
    /**
     * @var Behavior
     */
    public $owner = null;
    public function getFields() {
        return [];
    }

    /**
     * Example:
     * [
     *    [
     *      'class' => Page::class,
     *      'name' => 'pagesPage',
     *      'link' => [
     *          'id' => 'pages_page_id',
     *      ],
     *      'multiple' => false
     *    ]
     * ]
     *
     * @return array
     */
    public function getRelations() {
        return [];
    }

    public function rules() {
        return [];
    }

    public function initDataProvider($dataProvider) {
    }
}