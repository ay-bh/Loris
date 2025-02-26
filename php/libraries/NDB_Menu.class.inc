<?php declare(strict_types=1);

/**
 * This file contains the base class for an NDB_Menu. An NDB_Menu is the
 * base class for any menu page for a Loris module.
 *
 * PHP Version 5
 *
 * @category Main
 * @package  Main
 * @author   Loris Team <loris.mni@bic.mni.mcgill.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris/
 */

/**
 * Base Menu class
 *
 * @category Main
 * @package  Main
 * @author   Loris Team <loris.mni@bic.mni.mcgill.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris/
 */
class NDB_Menu extends NDB_Page
{
    /**
     * The menu's name
     *
     * @var    string
     * @access private
     */
    var $menu;

    /**
     * The menu's mode
     *
     * @var    string
     * @access private
     */
    var $mode;

    /**
     * Create a new NDB_Menu page
     *
     * @param \LORIS\LorisInstance $loris      The LORIS Instance that the page is
     *                                         serving serving
     * @param Module               $module     The test name being accessed
     * @param string               $page       The subtest being accessed (may be
     *                                         null)
     * @param string               $identifier The identifier for the data to load
     *                                         on this page
     * @param string               $commentID  The CommentID to load the data for
     */
    public function __construct(
        \LORIS\LorisInstance $loris,
        \Module $module,
        string $page,
        string $identifier,
        string $commentID
    ) {
        parent::__construct($loris, $module, $page, $identifier, $commentID);
        $this->menu = $module->getName();
    }

    /**
     * Displays the menu page
     *
     * @return string
     * @access public
     */
    function display()
    {
        if ($this->skipTemplate) {
            return "";
        }
        // dump the html for the menu
        $smarty = new Smarty_NeuroDB($this->Module->getName());
        $smarty->assign('mode', $this->mode);
        $smarty->assign($this->getTemplateData());
        $html = $smarty->fetch("menu_$this->menu.tpl");
        return $html;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    function toJSON(): string
    {
        return json_encode(['error' => 'Not implemented']);
    }
}
