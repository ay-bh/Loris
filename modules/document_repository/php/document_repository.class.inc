<?php
/**
 * This file handles the Document Repository for LORIS
 *
 * PHP Version 7
 *
 * @category Documentation
 * @package  Main
 * @author   Shen Wang <wangshen.mcin@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris
 */
namespace LORIS\document_repository;
/**
 * Document Repository Class
 *
 * This class is for the Document Repository
 *
 * @category Documentation
 * @package  Main
 * @author   Justin Kat <justinkat@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris
 */
class Document_Repository extends \NDB_Menu_Filter_Form
{
    private const PERMISSIONS = [
        'document_repository_view',
        'document_repository_delete',
        'document_repository_categories',
        'document_repository_upload_edit'
    ];
    public $AjaxModule        = true;
    public $skipTemplate      = true;

    /**
     * Checking user permissions
     *
     * @param \User $user The user whose access is being checked
     *
     * @return bool
     */
    function _hasAccess(\User $user) : bool
    {
        return $user->hasAnyPermission(self::PERMISSIONS);
    }
    /**
     * Function _setupVariables
     *
     * @return void
     */
    function setup()
    {
        parent::setup();
        $user  = \User::singleton();
        $db    = $this->loris->getDatabaseConnection();
        $query = $db->pselect(
            "SELECT * FROM document_repository_categories",
            []
        );
        //categories
        $categoriesList = [];
        foreach ($query as $value) {
               $arr = $this->parseCategory($value);
               $categoriesList[$arr['id']] =$arr['name'];
        }
        //site
        $siteList = [];

        // allow to view all sites data through filter
        if ($user->hasPermission('access_all_profiles')) {
            $siteList = \Utility::getSiteList(false);
            // Index sites using their names (used to filter react tables)
            foreach ($siteList as $key => $site) {
                unset($siteList[$key]);
                $siteList[$site] = $site;
            }
        } else {
            // allow only to view own site data
            $siteIDs = $user->getCenterIDs();
            foreach ($siteIDs as $val) {
                $site =& \Site::singleton($val);
                if ($site->isStudySite()) {
                    $siteList[$site->getCenterName()] = $site->getCenterName();
                }
            }
        }
        //instrument
        $instruments     = $db->pselectCol(
            "SELECT Test_name FROM test_names ORDER BY Test_name",
            []
        );
        $instrumentsList = array_combine($instruments, $instruments);

        //file types
        $fileTypeList  = [];
        $fileTypeQuery = $db->pselect(
            "SELECT File_type FROM document_repository group by File_type",
            []
        );
        foreach ($fileTypeQuery as $filetype) {
            $fileTypeList[$filetype['File_type']] = $filetype['File_type'];
        }
        $this->fieldOptions = [
            'fileCategories' => $categoriesList,
            'sites'          => $siteList,
            'instruments'    => $instrumentsList,
            'fileTypes'      => $fileTypeList,
            'hiddenFile'     => ['yes' => "Yes", 'no' => "No"],
        ];
        return;
    }
    /**
     * Handler of parsing category
     *
     * @param array $value the value
     *
     * @return array
     */
    function parseCategory($value): array
    {
        $id = $value['id'];
        $DB = $this->loris->getDatabaseConnection();
            $categoryName = $value['category_name'];
        do {
            if ($value['parent_id'] != 0) {
                $parent_id = $value['parent_id'];
                $query     = "SELECT * FROM document_repository_categories".
                            " where id=$parent_id";
                $value     = $DB->pselectRow($query, []);
                if (is_null($value)) {
                    throw new \LorisException(
                        'Could not find data for specified parent ID. '
                        . 'Cannot parse category.'
                    );
                }
                $categoryName = $value['category_name'] . ">" . $categoryName;
            }
        } while ($value['parent_id'] != 0);
         return [
             "name" => $categoryName,
             "id"   => $id,
         ];
    }

    /**
     * Build a list of media to display in Data Table
     *
     * @return void
     * @throws \DatabaseException
     */
    function _setupVariables()
    {
        $user = \User::singleton();
        // the base query
        $query  = " FROM document_repository v";
        $query .= " WHERE COALESCE(v.hide_video, false)=false";
        if (!$user->hasPermission('document_repository_hidden')) {
            $query .= " AND (hidden_file='no' OR hidden_file IS NULL)";
        }
        // set the class variables
        $this->columns  = [
            'v.File_name',
            'v.version',
            'v.File_type',
            'v.Instrument',
            'v.uploaded_by',
            '(SELECT name FROM psc WHERE CenterID=v.For_site) as site',
            'v.comments',
            'v.Date_uploaded',
            'v.record_id as Edit',
            'v.record_id as Delete_File',
            'v.File_category as category',
            'v.Data_dir',
        ];
        $this->query    = $query;
        $this->group_by = '';
        $this->order_by = 'v.File_name';
    }

    /**
     * Converts the results of this menu filter to a JSON format to be retrieved
     * with ?format=json
     *
     * @return string a json encoded string of the headers and data from this table
     */
    function toJSON(): string
    {
        $result = $this->toArray();
        $result['maxUploadSize'] = \Utility::getMaxUploadSize();
        return json_encode($result);
    }

    /**
     * Include additional CSS files:
     *  1. jQuery UI
     *  2. document_repository.css
     *
     * @return array of javascript to be inserted
     */
    function getCSSDependencies()
    {
        $factory = \NDB_Factory::singleton();
        $baseURL = $factory->settings()->getBaseURL();
        $deps    = parent::getCSSDependencies();
        return array_merge(
            $deps,
            [
                $baseURL . "/document_repository/css/document_repository.css",
            ]
        );
    }

    /**
     * Include the column formatter required to display the feedback link colours
     * in the candidate_list menu
     *
     * @return array of javascript to be inserted
     */
    function getJSDependencies()
    {
        $factory = \NDB_Factory::singleton();
        $baseURL = $factory->settings()->getBaseURL();
        $deps    = parent::getJSDependencies();
        return array_merge(
            $deps,
            [
                $baseURL . "/document_repository/js/docIndex.js",
            ]
        );
    }

}


