<?php

require_once 'vendors/Core/Inflector.php';

use Core\Inflector;

/**
 * Test class for RouteParser.
 * Generated by PHPUnit on 2012-01-28 at 14:32:16.
 */
class InflectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Core_InflectorTest
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        Inflector::reset();
    }


    public function testPluralize2()
    {
        $this->assertEquals("women", Inflector::pluralize("woman"));
        $this->assertEquals("children", Inflector::pluralize("child"));
        $this->assertEquals("people", Inflector::pluralize("person"));

        $this->assertEquals("kisses", Inflector::pluralize("kiss"));
        $this->assertEquals("phases", Inflector::pluralize("phase"));
        $this->assertEquals("dishes", Inflector::pluralize("dish"));
        $this->assertEquals("massages", Inflector::pluralize("massage"));
        $this->assertEquals("witches", Inflector::pluralize("witch"));
        $this->assertEquals("judges", Inflector::pluralize("judge"));

        $this->assertEquals("tests", Inflector::pluralize("test"));
        $this->assertEquals("laps", Inflector::pluralize("lap"));
        $this->assertEquals("cats", Inflector::pluralize("cat"));
        $this->assertEquals("clocks", Inflector::pluralize("clock"));
        $this->assertEquals("cuffs", Inflector::pluralize("cuff"));
        $this->assertEquals("deaths", Inflector::pluralize("death"));

        $this->assertEquals("boys", Inflector::pluralize("boy"));
        $this->assertEquals("girls", Inflector::pluralize("girl"));
        $this->assertEquals("chairs", Inflector::pluralize("chair"));

        $this->assertEquals("heroes", Inflector::pluralize("hero"));
        $this->assertEquals("potatoes", Inflector::pluralize("potato"));
        $this->assertEquals("volcanoes", Inflector::pluralize("volcano"));

        $this->assertEquals("cherries", Inflector::pluralize("cherry"));
        $this->assertEquals("ladies", Inflector::pluralize("lady"));

        $this->assertEquals("days", Inflector::pluralize("day"));
        $this->assertEquals("monkeys", Inflector::pluralize("monkey"));
    }

    // From here on, tests are taken from Lithium.

    /**
     * Tests singularization inflection rules
     *
     * @return void
     */
    public function testSingularize() {
        $this->assertEquals(Inflector::singularize('categorias'), 'categoria');
        $this->assertEquals(Inflector::singularize('menus'), 'menu');
        $this->assertEquals(Inflector::singularize('news'), 'news');
        $this->assertEquals(Inflector::singularize('food_menus'), 'food_menu');
        $this->assertEquals(Inflector::singularize('Menus'), 'Menu');
        $this->assertEquals(Inflector::singularize('FoodMenus'), 'FoodMenu');
        $this->assertEquals(Inflector::singularize('houses'), 'house');
        $this->assertEquals(Inflector::singularize('powerhouses'), 'powerhouse');
        $this->assertEquals(Inflector::singularize('quizzes'), 'quiz');
        $this->assertEquals(Inflector::singularize('Buses'), 'Bus');
        $this->assertEquals(Inflector::singularize('buses'), 'bus');
        $this->assertEquals(Inflector::singularize('matrix_rows'), 'matrix_row');
        $this->assertEquals(Inflector::singularize('matrices'), 'matrix');
        $this->assertEquals(Inflector::singularize('vertices'), 'vertex');
        $this->assertEquals(Inflector::singularize('indices'), 'index');
        $this->assertEquals(Inflector::singularize('Aliases'), 'Alias');
        $this->assertEquals(Inflector::singularize('Alias'), 'Alias');
        $this->assertEquals(Inflector::singularize('Media'), 'Media');
        $this->assertEquals(Inflector::singularize('alumni'), 'alumnus');
        $this->assertEquals(Inflector::singularize('bacilli'), 'bacillus');
        $this->assertEquals(Inflector::singularize('cacti'), 'cactus');
        $this->assertEquals(Inflector::singularize('foci'), 'focus');
        $this->assertEquals(Inflector::singularize('fungi'), 'fungus');
        $this->assertEquals(Inflector::singularize('nuclei'), 'nucleus');
        $this->assertEquals(Inflector::singularize('octopuses'), 'octopus');
        $this->assertEquals(Inflector::singularize('radii'), 'radius');
        $this->assertEquals(Inflector::singularize('stimuli'), 'stimulus');
        $this->assertEquals(Inflector::singularize('syllabi'), 'syllabus');
        $this->assertEquals(Inflector::singularize('termini'), 'terminus');
        $this->assertEquals(Inflector::singularize('viri'), 'virus');
        $this->assertEquals(Inflector::singularize('people'), 'person');
        $this->assertEquals(Inflector::singularize('gloves'), 'glove');
        $this->assertEquals(Inflector::singularize('doves'), 'dove');
        $this->assertEquals(Inflector::singularize('lives'), 'life');
        $this->assertEquals(Inflector::singularize('knives'), 'knife');
        $this->assertEquals(Inflector::singularize('wolves'), 'wolf');
        $this->assertEquals(Inflector::singularize('shelves'), 'shelf');
        $this->assertEquals(Inflector::singularize(''), '');
    }

    /**
     * Tests pluralization inflection rules
     *
     * @return void
     */
    public function testPluralize() {
        $this->assertEquals(Inflector::pluralize('categoria'), 'categorias');
        $this->assertEquals(Inflector::pluralize('house'), 'houses');
        $this->assertEquals(Inflector::pluralize('powerhouse'), 'powerhouses');
        $this->assertEquals(Inflector::pluralize('Bus'), 'Buses');
        $this->assertEquals(Inflector::pluralize('bus'), 'buses');
        $this->assertEquals(Inflector::pluralize('menu'), 'menus');
        $this->assertEquals(Inflector::pluralize('news'), 'news');
        $this->assertEquals(Inflector::pluralize('food_menu'), 'food_menus');
        $this->assertEquals(Inflector::pluralize('Menu'), 'Menus');
        $this->assertEquals(Inflector::pluralize('FoodMenu'), 'FoodMenus');
        $this->assertEquals(Inflector::pluralize('quiz'), 'quizzes');
        $this->assertEquals(Inflector::pluralize('matrix_row'), 'matrix_rows');
        $this->assertEquals(Inflector::pluralize('matrix'), 'matrices');
        $this->assertEquals(Inflector::pluralize('vertex'), 'vertices');
        $this->assertEquals(Inflector::pluralize('index'), 'indices');
        $this->assertEquals(Inflector::pluralize('Alias'), 'Aliases');
        $this->assertEquals(Inflector::pluralize('Aliases'), 'Aliases');
        $this->assertEquals(Inflector::pluralize('Media'), 'Media');
        $this->assertEquals(Inflector::pluralize('alumnus'), 'alumni');
        $this->assertEquals(Inflector::pluralize('bacillus'), 'bacilli');
        $this->assertEquals(Inflector::pluralize('cactus'), 'cacti');
        $this->assertEquals(Inflector::pluralize('focus'), 'foci');
        $this->assertEquals(Inflector::pluralize('fungus'), 'fungi');
        $this->assertEquals(Inflector::pluralize('nucleus'), 'nuclei');
        $this->assertEquals(Inflector::pluralize('octopus'), 'octopuses');
        $this->assertEquals(Inflector::pluralize('radius'), 'radii');
        $this->assertEquals(Inflector::pluralize('stimulus'), 'stimuli');
        $this->assertEquals(Inflector::pluralize('syllabus'), 'syllabi');
        $this->assertEquals(Inflector::pluralize('terminus'), 'termini');
        $this->assertEquals(Inflector::pluralize('virus'), 'viri');
        $this->assertEquals(Inflector::pluralize('person'), 'people');
        $this->assertEquals(Inflector::pluralize('people'), 'people');
        $this->assertEquals(Inflector::pluralize('glove'), 'gloves');
        $this->assertEquals(Inflector::pluralize(''), '');

        $result = Inflector::pluralize('errata');
        $this->assertNull(Inflector::rules('plural', array('/rata/' => '\1ratum')));
        $this->assertEquals(Inflector::pluralize('errata'), $result);

        Inflector::reset();
        $this->assertNotEquals(Inflector::pluralize('errata'), $result);
    }

    /**
     * testInflectorSlug method
     *
     * @return void
     */
    public function testSlug() {
        $result = Inflector::slug('Foo Bar: Not just for breakfast any-more');
        $expected = 'Foo-Bar-Not-just-for-breakfast-any-more';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('Foo Bar: Not just for breakfast any-more', '_');
        $expected = 'Foo_Bar_Not_just_for_breakfast_any_more';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('this/is/a/path', '_');
        $expected = 'this_is_a_path';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('Foo Bar: Not just for breakfast any-more', "+");
        $expected = 'Foo+Bar+Not+just+for+breakfast+any+more';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('Äpfel Über Öl grün ärgert groß öko');
        $expected = 'Aepfel-Ueber-Oel-gruen-aergert-gross-oeko';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('The truth - and- more- news');
        $expected = 'The-truth-and-more-news';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('The truth: and more news');
        $expected = 'The-truth-and-more-news';
        $this->assertEquals($expected, $result);

        $message = 'La langue française est un attribut de souveraineté en France';
        $result = Inflector::slug($message, '-');
        $expected = 'La-langue-francaise-est-un-attribut-de-souverainete-en-France';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('!@$#exciting stuff! - what !@-# was that?');
        $expected = 'exciting-stuff-what-was-that';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('20% of profits went to me!');
        $expected = '20-of-profits-went-to-me';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('#this melts your face1#2#3');
        $expected = 'this-melts-your-face1-2-3';
        $this->assertEquals($expected, $result);

        $result = Inflector::slug('ThisMeltsYourFace');
        $expected = 'This-Melts-Your-Face';
        $this->assertEquals($expected, $result);
    }

    public function testAddingInvalidRules() {
        $before = array(
            Inflector::rules('singular'),
            Inflector::rules('plural'),
            Inflector::rules('transliteration')
        );
        $this->assertNull(Inflector::rules('foo'));
        $this->assertEquals($before, array(
            Inflector::rules('singular'),
            Inflector::rules('plural'),
            Inflector::rules('transliteration')
        ));
    }

    public function testAddingSingularizationRules() {
        $before = Inflector::rules('singular');
        $result = Inflector::singularize('errata');
        $this->assertNull(Inflector::rules('singular', array('/rata/' => '\1ratus')));
        $this->assertEquals(Inflector::singularize('errata'), $result);

        Inflector::reset();
        $this->assertNotEquals(Inflector::singularize('errata'), $result);

        $after = Inflector::rules('singular');
        $expected = array(
            'rules', 'irregular', 'uninflected', 'regexUninflected', 'regexIrregular'
        );
        $this->assertEquals(array_keys($before), $expected);
        $this->assertEquals(array_keys($after), $expected);

        $result = array_diff($after['rules'], $before['rules']);
        $this->assertEquals($result, array('/rata/' => '\1ratus'));

        foreach (array('irregular', 'uninflected', 'regexUninflected', 'regexIrregular') as $key) {
            $this->assertEquals($before[$key], $after[$key]);
        }

        $this->assertNull(Inflector::rules('singular', array('rules' => array(
            '/rata/' => '\1ratus'
        ))));
        $this->assertEquals(Inflector::rules('singular'), $after);
    }

    /**
     * Tests that rules for uninflected singular words are kept in sync with the plural, and vice
     * versa.
     *
     * @return void
     */
    public function testIrregularWords() {
        $expectedPlural = Inflector::rules('plural');
        $this->assertFalse(isset($expectedPlural['irregular']['bar']));

        $expectedSingular = Inflector::rules('singular');
        $this->assertFalse(isset($expectedSingular['irregular']['foo']));

        Inflector::rules('singular', array('irregular' => array('foo' => 'bar')));

        $resultSingular = Inflector::rules('singular');
        $this->assertEquals($resultSingular['irregular']['foo'], 'bar');
        unset($resultSingular['irregular']['foo']);

        $this->assertEquals($resultSingular, $expectedSingular);

        $resultPlural = Inflector::rules('plural');
        $this->assertEquals($resultPlural['irregular']['bar'], 'foo');
        unset($resultPlural['irregular']['bar']);

        $this->assertEquals($resultPlural, $expectedPlural);
    }

    /**
     * testVariableNaming method
     *
     * @return void
     */
    public function testCamelize() {
        $this->assertEquals(Inflector::camelize('test-field'), 'TestField');
        $this->assertEquals(Inflector::camelize('test_field'), 'TestField');
        $this->assertEquals(Inflector::camelize('test_fieLd', false), 'testFieLd');
        $this->assertEquals(Inflector::camelize('test field', false), 'testField');
        $this->assertEquals(Inflector::camelize('Test_field', false), 'testField');
    }

    /**
     * testClassNaming method
     *
     * @return void
     */
    public function testClassify() {
        $this->assertEquals(Inflector::classify('artists_genres'), 'ArtistsGenre');
        $this->assertEquals(Inflector::classify('file_systems'), 'FileSystem');
        $this->assertEquals(Inflector::classify('news'), 'News');
    }

    /**
     * testTableNaming method
     *
     * @return void
     */
    public function testTabelize() {
        $this->assertEquals(Inflector::tableize('ArtistsGenre'), 'artists_genres');
        $this->assertEquals(Inflector::tableize('FileSystem'), 'file_systems');
        $this->assertEquals(Inflector::tableize('News'), 'news');
    }

    /**
     * testHumanization method
     *
     * @return void
     */
    public function testHumanize() {
        $this->assertEquals(Inflector::humanize('posts'), 'Posts');
        $this->assertEquals(Inflector::humanize('posts_tags'), 'Posts Tags');
        $this->assertEquals(Inflector::humanize('file_systems'), 'File Systems');
        $this->assertEquals(Inflector::humanize('the-post-title', '-'), 'The Post Title');
    }

    /**
     * Tests adding transliterated characters to the map used in `Inflector::slug()`.
     *
     * @return void
     */
    public function testAddTransliterations() {
        $this->assertEquals(Inflector::slug('Montréal'), 'Montreal');
        $this->assertNotEquals(Inflector::slug('Écaussines'), 'Ecaussines');

        Inflector::rules('transliteration', array('/É|Ê/' => 'E'));
        $this->assertEquals(Inflector::slug('Écaussines-d\'Enghien'), 'Ecaussines-d-Enghien');

        $this->assertNotEquals(Inflector::slug('JØRGEN'), 'JORGEN');
        Inflector::rules('transliteration', array('/Ø/' => 'O'));
        $this->assertEquals(Inflector::slug('JØRGEN'), 'JORGEN');

        $this->assertNotEquals(Inflector::slug('ÎÍ'), 'II');
        Inflector::rules('transliteration', array('/Î|Í/' => 'I'));
        $this->assertEquals(Inflector::slug('ÎÍ'), 'II');

        $this->assertEquals(Inflector::slug('ABc'), 'ABc');
        Inflector::rules('transliteration', array('AB' => 'a'));
        $this->assertEquals(Inflector::slug('ABc'), 'aac');
    }

    public function testAddingUninflectedWords() {
        $this->assertEquals(Inflector::pluralize('bord'), 'bords');
        Inflector::rules('uninflected', 'bord');
        $this->assertEquals(Inflector::pluralize('bord'), 'bord');
    }

    /**
     * Tests the storage mechanism for `$_underscored`, `$_camelized`,
     *  `$_humanized` and `$_pluralized`.
     *
     * @return void
     */
    /*
    public function testStorageMechanism() {
        Inflector::reset();

        $expected = array('TestField' => 'test_field');
        $this->assertFalse($this->getProtectedValue('$_underscored'));
        $this->assertEquals(Inflector::underscore('TestField'), 'test_field');
        $this->assertEquals($expected, $this->getProtectedValue('$_underscored'));
        $this->assertEquals(Inflector::underscore('TestField'), 'test_field');

        $expected = array('test_field' => 'TestField');
        $this->assertFalse($this->getProtectedValue('$_camelized'));
        $this->assertEquals(Inflector::camelize('test_field', true), 'TestField');
        $this->assertEquals($expected, $this->getProtectedValue('$_camelized'));
        $this->assertEquals(Inflector::camelize('test_field', true), 'TestField');

        $expected = array('test_field:_' => 'Test Field');
        $this->assertFalse($this->getProtectedValue('$_humanized'));
        $this->assertEquals(Inflector::humanize('test_field'), 'Test Field');
        $this->assertEquals($expected, $this->getProtectedValue('$_humanized'));
        $this->assertEquals(Inflector::humanize('test_field'), 'Test Field');

        $expected = array('field' => 'fields');
        $this->assertFalse($this->getProtectedValue('$_pluralized'));
        $this->assertEquals(Inflector::pluralize('field'), 'fields');
        $this->assertEquals($expected, $this->getProtectedValue('$_pluralized'));
        $this->assertEquals(Inflector::pluralize('field'), 'fields');
    }
    */

    /**
     * This is a helper method for testStorageMechanism to fetch a private
     * property of the Inflector class.
     *
     * @param string $property
     * @return string The value of the property.
     */
    /*
    private function getProtectedValue($property) {
            $info = Inspector::info("lithium\util\Inflector::{$property}");
            return $info['value'];
    }
    */

}

?>