<?php declare(strict_types=1);

/**
 * Unit tests for NDB_Page class
 *
 * PHP Version 7
 *
 * @category Tests
 * @package  Main
 * @author   Alexandra Livadas <alexandra.livadas@mcin.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris/
 */
use PHPUnit\Framework\TestCase;
/**
 * Unit tests for NDB_Config class
 *
 * @category Tests
 * @package  Main
 * @author   Shen Wang <alexandra.livadas@mcin.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris/
 */
class NDB_PageTest extends TestCase
{

    /**
     * The NDB_Page object used for testing
     *
     * @var NDB_Page
     */
    private $_page;

    /**
     * The Module object used for testing
     *
     * @var Module
     */
    private $_module;

    /**
     * This method is called before each test is executed
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $mockconfig = $this->getMockBuilder('NDB_Config')->getMock();
        $mockdb     = $this->getMockBuilder('Database')->getMock();

        '@phan-var \Database $mockdb';
        '@phan-var \NDB_Config $mockconfig';

        $loris         = new \LORIS\LorisInstance($mockdb, $mockconfig, []);
        $this->_module = new NullModule($loris, "test_module");
        $this->_page   = new NDB_Page(
            $loris,
            $this->_module,
            "test_page",
            "515",
            "123"
        );
    }

    /**
     * This method is called after each test is executed
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test that the _construct function sets all variables correctly
     *
     * @covers NDB_Page::_construct
     * @return void
     */
    public function testConstruct()
    {
        $this->assertEquals("test_module", $this->_page->name);
        $this->assertEquals("test_page", $this->_page->page);
        $this->assertEquals("515", $this->_page->identifier);
        $this->assertEquals("123", $this->_page->commentID);
        $this->assertEquals("test_page", $this->_page->template);
    }

    /**
     * Test that setTemplateVar correctly sets a value in the
     * tpl_data array and that getTemplateData returns the correct information
     *
     * @covers NDB_Page::setTemplateVar
     * @covers NDB_Page::getTemplateData
     * @return void
     */
    public function testSetAndGetTemplateVar()
    {
        $this->_page->setTemplateVar("test_var", "test_value");
        $data = $this->_page->getTemplateData();
        $this->assertEquals("test_value", $data['test_var']);
    }

    /**
     * Test that addFile calls addElement from LorisForm and properly adds
     * an element to the page's form
     *
     * @covers NDB_Page::addFile
     * @return void
     */
    public function testAddFile()
    {
        $this->_page->addFile("test_name", "test_label");
        $this->assertEquals(
            ['name'  => 'test_name',
                'label' => 'test_label',
                'type'  => 'file',
                'class' => 'fileUpload'
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addHeader calls addElement from LorisForm and properly adds
     * an element to the page's form
     *
     * @covers NDB_Page::addHeader
     * @return void
     */
    public function testAddHeader()
    {
        $this->_page->addHeader("test_header");
        $this->assertEquals(
            ['label' => 'test_header',
                'type'  => 'header'
            ],
            current($this->_page->form->form)
        );
    }

    /**
     * Test that addSelect calls addElement from LorisForm and properly adds
     * an element to the page's form
     *
     * @covers NDB_Page::addSelect
     * @return void
     */
    public function testAddSelect()
    {
        $this->_page->addSelect("test_name", "test_label", []);
        $this->assertEquals(
            ['name'    => 'test_name',
                'label'   => 'test_label',
                'type'    => 'select',
                'class'   => 'form-control input-sm',
                'options' => []
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addLabel calls addElement from LorisForm and properly adds
     * an element to the page's form
     *
     * @covers NDB_Page::addLabel
     * @return void
     */
    public function testAddLabel()
    {
        $this->_page->addLabel("test_label");
        $this->assertEquals(
            ['label' => 'test_label',
                'type'  => 'static'
            ],
            current($this->_page->form->form)
        );
    }

    /**
     * Test that addScoreColumn calls addElement from LorisForm and properly adds
     * an element to the page's form
     *
     * @covers NDB_Page::addScoreColumn
     * @return void
     */
    public function testAddScoreColumn()
    {
        $this->_page->addScoreColumn("test_name", "test_label");
        $this->assertEquals(
            ['name'    => 'test_name',
                'label' => 'test_label',
                'type'  => 'static'
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addBasicText calls addElement from LorisForm and properly adds
     * an element to the page's form
     *
     * @covers NDB_Page::addBasicText
     * @return void
     */
    public function testAddBasicText()
    {
        $this->_page->addBasicText("test_name", "test_label");
        $this->assertEquals(
            ['name'    => 'test_name',
                'label' => 'test_label',
                'type'  => 'text',
                'class' => 'form-control input-sm'
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addBasicTextArea calls addElement from LorisForm and properly adds
     * an element to the page's form
     *
     * @covers NDB_Page::addBasicTextArea
     * @return void
     */
    public function testAddBasicTextArea()
    {
        $this->_page->addBasicTextArea("test_name", "test_label");
        $this->assertEquals(
            ['name'    => 'test_name',
                'label' => 'test_label',
                'type'  => 'textarea',
                'class' => 'form-control input-sm'
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addBasicDate calls addElement from LorisForm and properly adds
     * an element to the page's form when dateOptions is not set. Since
     * dateOptions is not set, the options array should remain empty.
     *
     * @covers NDB_Page::addBasicDate
     * @return void
     */
    public function testAddBasicDateWithNoDateOptions()
    {
        $this->_page->addBasicDate("test_name", "test_label");
        $this->assertEquals(
            ['name'    => 'test_name',
                'label'   => 'test_label',
                'type'    => 'date',
                'class'   => 'form-control input-sm',
                'options' => []
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addBasicDate calls addElement from LorisForm and properly adds
     * an element to the page's form when dateOptions is set. Since
     * dateOptions is set, the options array should have information in it.
     *
     * @covers NDB_Page::addBasicDate
     * @return void
     */
    public function testAddBasicDateWithDateOptionsSet()
    {
        $this->_page->dateOptions = ['someOption' => 'true'];
        $this->_page->addBasicDate("test_name", "test_label");
        $this->assertEquals(
            ['name'    => 'test_name',
                'label'   => 'test_label',
                'type'    => 'date',
                'class'   => 'form-control input-sm',
                'options' => ['someOption' => 'true']
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addCheckbox calls addElement from LorisForm and properly adds
     * an element to the page's form
     * TODO: Update this test once addCheckbox method is fixed.
     *
     * @covers NDB_Page::addCheckbox
     * @return void
     */
    public function testAddCheckbox()
    {
        $this->_page->addCheckbox("test_name", "test_label", []);
        $this->assertEquals(
            ['name'    => 'test_name',
                'label' => 'test_label',
                'type'  => 'advcheckbox',
                'class' => 'form-control input-sm'
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addRadio calls LorisForm::addGroup and properly creates a
     * group of radio elements.
     *
     * @covers NDB_Page::addRadio
     * @return void
     */
    public function testAddRadio()
    {
        $radios        = [
            ['label' => 'label1',
                'value' => 'value1'
            ],
            ['label' => 'label2',
                'value' => 'value2'
            ]
        ];
        $elementsArray = [
            ['name' => 'test_radio',
                'label' => 'label1',
                'value' => 'value1',
                'type'  => 'radio',
                'class' => 'form-control input-sm'
            ],
            ['name' => 'test_radio',
                'label' => 'label2',
                'value' => 'value2',
                'type'  => 'radio',
                'class' => 'form-control input-sm'
            ]
        ];
        $this->_page->addRadio("test_radio", "group_label", $radios);
        $this->assertEquals(
            ['name' => 'test_radio_group',
                'label'     => 'group_label',
                'type'      => 'group',
                'delimiter' => ' ',
                'options'   => false,
                'elements'  => $elementsArray
            ],
            $this->_page->form->form['test_radio_group']
        );
    }

    /**
     * Test that addHidden calls addElement from LorisForm and properly adds
     * an element to the page's form
     *
     * @covers NDB_Page::addHidden
     * @return void
     */
    public function testAddHidden()
    {
        $this->_page->addHidden("test_name", "test_value");
        $this->assertEquals(
            ['name'    => 'test_name',
                'label' => null,
                'value' => 'test_value',
                'type'  => 'hidden'
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addTextAreaGroup adds a group of elements that consists of
     * a text area element and a 'not_answered' select element.
     *
     * @covers NDB_Page::addTextAreaGroup
     * @return void
     */
    public function testAddTextAreaGroup()
    {
        $options       = [''             => '',
            'not_answered' => 'Not Answered'
        ];
        $elementsArray = [
            ['name' => 'test_field',
                'label' => '',
                'type'  => 'textarea',
                'class' => 'form-control input-sm'
            ],
            ['name' => 'test_field_status',
                'label'   => '',
                'type'    => 'select',
                'options' => $options,
                'class'   => 'form-control input-sm not-answered'
            ]
        ];

        $this->_page->addTextAreaGroup("test_field", "test_label");
        $this->assertEquals(
            ['name' => 'test_field_group',
                'type'      => 'group',
                'label'     => 'test_label',
                'delimiter' => ' ',
                'options'   => false,
                'elements'  => $elementsArray
            ],
            $this->_page->form->form['test_field_group']
        );
    }

    /**
     * Test that addPassword calls addElement from LorisForm and properly adds
     * an element to the page's form
     *
     * @covers NDB_Page::addPassword
     * @return void
     */
    public function testAddPassword()
    {
        $this->_page->addPassword("test_name", "test_label");
        $this->assertEquals(
            ['name'    => 'test_name',
                'label' => 'test_label',
                'type'  => 'password',
                'class' => 'form-control input-sm'
            ],
            $this->_page->form->form['test_name']
        );
    }

    /**
     * Test that addRule calls the LorisForm::addRule method and adds
     * the rule attribute to the given element
     *
     * @covers NDB_Page::addRule
     * @return void
     */
    public function testAddRule()
    {
        $this->_page->addBasicText("abc", "Hello", []);
        $this->_page->addRule("abc", "Required!", "required");
        $this->assertTrue($this->_page->form->form['abc']['required']);
        $this->assertEquals(
            'Required!',
            $this->_page->form->form['abc']['requireMsg']
        );
    }

    /**
     * Test that addGroup calls LorisForm::addGroup and adds a group of elements
     * to the form correctly
     *
     * @covers NDB_Page::addGroup
     * @return void
     */
    public function testAddGroup()
    {
        $text1 = $this->_page->createText("test_name1", "test_label1");
        $text2 = $this->_page->createText("test_name2", "test_label2");
        $this->_page->addGroup(
            [$text1, $text2],
            "test_group",
            "group_label",
            ", "
        );
        $result = ['name'      => 'test_group',
            'type'      => 'group',
            'label'     => 'group_label',
            'delimiter' => ', ',
            'options'   => null,
            'elements'  => [$text1, $text2]
        ];
        $this->assertEquals(
            $result,
            $this->_page->form->form['test_group']
        );
    }

    /**
     * Test that addGroupRule calls LorisForm::addGroupRule and adds the correct
     * rules and rule messages to the given group's elements
     *
     * @covers NDB_Page::addGroupRule
     * @return void
     */
    public function testAddGroupRule()
    {
        $text1 = $this->_page->createText("test_name1", "test_label1");
        $text2 = $this->_page->createText("test_name2", "test_label2");
        $this->_page->addGroup(
            [$text1, $text2],
            "test_group",
            "group_label",
            ", "
        );
        $testRules = [
            [
                ["Message for text1!", 'required']
            ],
            [
                ["Message for text2!", 'numeric']
            ]
        ];
        $this->_page->addGroupRule("test_group", $testRules);

        $this->assertTrue(
            $this->_page->form->form['test_group']['elements'][0]['required']
        );
        $this->assertEquals(
            "Message for text1!",
            $this->_page->form->form['test_group']['elements'][0]['requireMsg']
        );
        $this->assertTrue(
            $this->_page->form->form['test_group']['elements'][1]['numeric']
        );
        $this->assertEquals(
            "Message for text2!",
            $this->_page->form->form['test_group']['elements'][1]['numericMsg']
        );
    }

    /**
     * Test that createSelect returns an array representing a select element
     *
     * @covers NDB_Page::createSelect
     * @return void
     */
    public function testCreateSelect()
    {
        $this->assertEquals(
            ['name' => 'test_field',
                'label'   => 'test_label',
                'type'    => 'select',
                'class'   => 'form-control input-sm',
                'options' => null
            ],
            $this->_page->createSelect("test_field", "test_label")
        );
    }

    /**
     * Test that createLabel returns an array representing a label element
     *
     * FIXME: This is incomplete because the createLabel function needs
     *        to be updated. See the fixme comment above NDB_Page::createLabel
     *        - Alexandra Livadas
     *
     * @covers NDB_Page::createLabel
     * @return void
     */
    public function testCreateLabel()
    {
        $this->markTestIncomplete("This test is incomplete");
    }

    /**
     * Test that createText returns an array representing a text element
     *
     * @covers NDB_Page::createText
     * @return void
     */
    public function testCreateText()
    {
        $this->assertEquals(
            ['name' => 'test_field',
                'label' => 'test_label',
                'type'  => 'text',
                'class' => 'form-control input-sm'
            ],
            $this->_page->createText("test_field", "test_label")
        );
    }

    /**
     * Test that createTextArea returns an array representing a textarea element
     *
     * @covers NDB_Page::createTextArea
     * @return void
     */
    public function testCreateTextArea()
    {
        $this->assertEquals(
            ['name' => 'test_field',
                'label' => 'test_label',
                'type'  => 'textarea',
                'class' => 'form-control input-sm'
            ],
            $this->_page->createTextArea("test_field", "test_label")
        );
    }

    /**
     * Test that createDate returns an array representing a date element
     *
     * @covers NDB_Page::createDate
     * @return void
     */
    public function testCreateDate()
    {
        $this->assertEquals(
            ['name' => 'test_field',
                'label'   => 'test_label',
                'type'    => 'date',
                'class'   => 'form-control input-sm',
                'options' => null
            ],
            $this->_page->createDate("test_field", "test_label")
        );
    }

    /**
     * Test that createCheckbox returns an array representing an advcheckbox element
     *
     * @covers NDB_Page::createCheckbox
     * @return void
     */
    public function testCreateCheckbox()
    {
        $this->assertEquals(
            ['name' => 'test_field',
                'label' => 'test_label',
                'type'  => 'advcheckbox'
            ],
            $this->_page->createCheckbox("test_field", "test_label")
        );
    }

    /**
     * Test that createRadio returns an array representing a radio element
     *
     * @covers NDB_Page::createRadio
     * @return void
     */
    public function testCreateRadio()
    {
        $this->assertEquals(
            ['name' => 'test_field',
                'label' => 'test_label',
                'type'  => 'radio'
            ],
            $this->_page->createRadio("test_field", "test_label")
        );
    }

    /**
     * Test that createPassword returns an array representing a password element
     *
     * @covers NDB_Page::createPassword
     * @return void
     */
    public function testCreatePassword()
    {
        $this->assertEquals(
            ['name' => 'test_field',
                'label' => 'test_label',
                'type'  => 'password',
                'class' => 'form-control input-sm'
            ],
            $this->_page->createPassword("test_field", "test_label")
        );
    }

    /**
     * Test that display returns an empty string if skipTemplate is true
     *
     * @covers NDB_Page::display
     * @return void
     */
    public function testDisplayWithSkipTemplateTrue()
    {
        $this->_page->skipTemplate = true;
        $this->assertEquals("", $this->_page->display());
    }

    /**
     * Test that display uses a Smarty_NeuroDB object to return an html of the
     * page object.
     *
     * @covers NDB_Page::display
     * @return void
     *
     * @note This test is incomplete because it uses Smarty_NeuroDB which needs
     *       to use a mock object. - Alexandra Livadas
     */
    public function testDisplayWithFormFrozen()
    {
        $this->markTestIncomplete("This test is incomplete!");
        $configMock = $this->getMockBuilder('NDB_Config')->getMock();
        '@phan-var \NDB_Config $configMock';
        $factory = NDB_Factory::singleton();
        $factory->setConfig($configMock);
        $smarty = $this->getMockBuilder(Smarty_NeuroDB::class)
            ->disableOriginalConstructor()
            ->getMock();
        $smarty->expects($this->any())->method('fetch')
            ->willReturn("fetch was called!");
        $this->_page->form->freeze();
        $this->assertEquals("fetch was called!", $this->_page->display());
    }

    /**
     * Test that the _setDefaults method correctly sets the default value
     * and returns the default array and that _getDefaults returns the
     * correct information.
     *
     * @covers NDB_Page::_setDefaults
     * @covers NDB_Page::_getDefaults
     * @return void
     */
    public function testGetSetDefaults()
    {
        $defaults = $this->_page->_setDefaults([1, 2, 3]);
        $this->assertEquals($defaults, $this->_page->_getDefaults());
    }

    /**
     * Test that toJSON returns the correctly formatted information
     *
     * @covers NDB_Page::toJSON
     * @return void
     */
    public function testToJSON()
    {
        $this->assertEquals('{"error":"Not implemented"}', $this->_page->toJSON());
    }

    /**
     * Test that _hasAccess returns true
     *
     * @covers NDB_Page::_hasAccess
     * @return void
     */
    public function testHasAccess()
    {
        $user = $this->getMockBuilder('\User')->getMock();
        '@phan-var \User $user';
        $this->assertTrue($this->_page->_hasAccess($user));
    }

    /**
     * Test that getBreadcrumbs returns a BreadcrumbTrail object with the
     * correct values, given that the name and page of the NDB_Page object
     * are not the same.
     *
     * @covers NDB_Page::getBreadcrumbs
     * @return void
     */
    public function testGetBreadcrumbs()
    {
        $name     = $this->_page->name;
        $page     = $this->_page->page;
        $sublabel = ucwords(str_replace('_', ' ', $page));
        $this->assertEquals(
            new \LORIS\BreadcrumbTrail(
                new \LORIS\Breadcrumb($this->_page->Module->getLongName(), "/$name"),
                new \LORIS\Breadcrumb($sublabel, "/$name/$page")
            ),
            $this->_page->getBreadcrumbs()
        );
    }

    /**
     * Test that getBreadcrumbs returns the correct BreadcrumbTrail object
     * given that the page and name have the same value.
     *
     * @covers NDB_Page::getBreadcrumbs
     * @return void
     */
    public function testGetBreadcrumbsWithSamePageAndName()
    {
        $this->_page->name = "page_name";
        $this->_page->page = "page_name";
        $name = $this->_page->name;
        $this->assertEquals(
            new \LORIS\BreadcrumbTrail(
                new \LORIS\Breadcrumb($this->_page->Module->getLongName(), "/$name")
            ),
            $this->_page->getBreadcrumbs()
        );
    }

    /**
     * Test that getJSDependencies returns the correct array of dependencies
     *
     * @covers NDB_Page::getJSDependencies
     * @return void
     */
    public function testGetJSDependencies()
    {
        $configMock = $this->getMockBuilder('NDB_Config')->getMock();
        '@phan-var \NDB_Config $configMock';

        $factory = NDB_Factory::singleton();
        $factory->setConfig($configMock);
        $this->assertEquals(
            [
                '/js/jquery/jquery-1.11.0.min.js',
                '/js/loris-scripts.js',
                '/js/modernizr/modernizr.min.js',
                '/js/polyfills.js',
                '/vendor/js/react/react.production.min.js',
                '/vendor/js/react/react-dom.production.min.js',
                '/js/jquery/jquery-ui-1.10.4.custom.min.js',
                '/js/jquery.dynamictable.js',
                '/js/jquery.fileupload.js',
                '/bootstrap/js/bootstrap.min.js',
                '/js/components/Breadcrumbs.js',
                '/js/util/queryString.js',
                '/js/components/Help.js',
            ],
            $this->_page->getJSDependencies()
        );
    }

    /**
     * Test that getCSSDependencies returns the correct array of dependencies
     *
     * @covers NDB_Page::getCSSDependencies
     * @return void
     */
    public function testGetCSSDependencies()
    {
        $configMock = $this->getMockBuilder('NDB_Config')->getMock();
        '@phan-var \NDB_Config $configMock';

        $factory = NDB_Factory::singleton();
        $factory->setConfig($configMock);
        $this->assertEquals(
            [
                '/bootstrap/css/bootstrap.min.css',
                '/bootstrap/css/custom-css.css',
                '/js/jquery/datepicker/datepicker.css'
            ],
            $this->_page->getCSSDependencies()
        );
    }
}
