<?php

require_once 'HTMLPurifier/StrategyHarness.php';
require_once 'HTMLPurifier/Strategy/MakeWellFormed.php';

class HTMLPurifier_Strategy_MakeWellFormedTest extends HTMLPurifier_StrategyHarness
{
    
    function setUp() {
        parent::setUp();
        $this->obj = new HTMLPurifier_Strategy_MakeWellFormed();
        $this->config = array();
    }
    
    function testNormalIntegration() {
        $this->assertResult('');
        $this->assertResult('This is <b>bold text</b>.');
    }
    
    function testUnclosedTagIntegration() {
        $this->assertResult(
            '<b>Unclosed tag, gasp!',
            '<b>Unclosed tag, gasp!</b>'
        );
        
        $this->assertResult(
            '<b><i>Bold and italic?</b>',
            '<b><i>Bold and italic?</i></b>'
        );
        
        $this->assertResult(
            'Unused end tags... recycle!</b>',
            'Unused end tags... recycle!'
        );
    }
    
    function testEmptyTagDetectionIntegration() {
        $this->assertResult(
            '<br style="clear:both;">',
            '<br style="clear:both;" />'
        );
        
        $this->assertResult(
            '<div style="clear:both;" />',
            '<div style="clear:both;"></div>'
        );
    }
    
    function testAutoClose() {
        // paragraph
        
        $this->assertResult(
            '<p>Paragraph 1<p>Paragraph 2',
            '<p>Paragraph 1</p><p>Paragraph 2</p>'
        );
        
        $this->assertResult(
            '<div><p>Paragraphs<p>In<p>A<p>Div</div>',
            '<div><p>Paragraphs</p><p>In</p><p>A</p><p>Div</p></div>'
        );
        
        // list
        
        $this->assertResult(
            '<ol><li>Item 1<li>Item 2</ol>',
            '<ol><li>Item 1</li><li>Item 2</li></ol>'
        );
        
        // colgroup
        
        $this->assertResult(
            '<table><colgroup><col /><tr></tr></table>',
            '<table><colgroup><col /></colgroup><tr></tr></table>'
        );
        
    }
    
    function testAutoParagraph() {
        $this->config = array('AutoFormat.AutoParagraph' => true);
        
        $this->assertResult(
            'Foobar',
            '<p>Foobar</p>'
        );
        
        $this->assertResult(
'Par 1
Par 1 still',
'<p>Par 1
Par 1 still</p>'
        );
        
        $this->assertResult(
'Par1

Par2',
            '<p>Par1</p><p>Par2</p>'
        );
        
        $this->assertResult(
'Par1

 

Par2',
            '<p>Par1</p><p>Par2</p>'
        );
        
        $this->assertResult(
'<b>Par1</b>

<i>Par2</i>',
            '<p><b>Par1</b></p><p><i>Par2</i></p>'
        );
        
        
        $this->assertResult(
'<b>Par1

Par2</b>',
'<p><b>Par1

Par2</b></p>'
        );
        
        $this->assertResult(
            'Par1<p>Par2</p>',
            '<p>Par1</p><p>Par2</p>'
        );
        
        $this->assertResult(
            '<b>Par1',
            '<p><b>Par1</b></p>'
        );
        
        $this->assertResult(
'<pre>Par1

Par1</pre>'
        );
        
        $this->assertResult(
'Par1

  ',
'<p>Par1</p>'
        );
        $this->assertResult(
'Par1

<div>Par2</div>

Par3',
'<p>Par1</p><div>Par2</div><p>Par3</p>'
        );
        
        $this->assertResult(
'Par<b>1</b>',
            '<p>Par<b>1</b></p>'
        );
        
        $this->assertResult(
'

Par',
            '<p>Par</p>'
        );
        
        $this->assertResult(
'

Par

',
            '<p>Par</p>'
        );
        
        $this->assertResult(
'<div>Par1

Par2</div>',
            '<div><p>Par1</p><p>Par2</p></div>'
        );
        
        $this->assertResult(
'<div><b>Par1</b>

Par2</div>',
            '<div><p><b>Par1</b></p><p>Par2</p></div>'
        );
        
        $this->assertResult('<div>Par1</div>');
        
        $this->assertResult(
'<div><b>Par1</b>

<i>Par2</i></div>',
            '<div><p><b>Par1</b></p><p><i>Par2</i></p></div>'
        );
        
        $this->assertResult(
'<pre><b>Par1</b>

<i>Par2</i></pre>',
            true
        );
        
        $this->assertResult(
'<div><p>Foo

Bar</p></div>',
            '<div><p>Foo</p><p>Bar</p></div>'
        );
        
        $this->assertResult(
'<div><p><b>Foo</b>

<i>Bar</i></p></div>',
            '<div><p><b>Foo</b></p><p><i>Bar</i></p></div>'
        );
        
        $this->assertResult(
'<div><b>Foo</b></div>',
            '<div><b>Foo</b></div>'
        );
        
        $this->assertResult(
'<blockquote>Par1

Par2</blockquote>',
            '<blockquote><p>Par1</p><p>Par2</p></blockquote>'
        );
        
        $this->assertResult(
'<ul><li>Foo</li>

<li>Bar</li></ul>', true
        );
        
        $this->assertResult(
'<div>

Bar

</div>', 
        '<div><p>Bar</p></div>'
        );
        
        $this->assertResult(
'<b>Par1</b>a



Par2', 
        '<p><b>Par1</b>a</p><p>Par2</p>'
        );
        
        $this->assertResult(
'Par1

Par2</p>', 
        '<p>Par1</p><p>Par2</p>'
        );
        
        $this->assertResult(
'Par1

Par2</div>', 
        '<p>Par1</p><p>Par2</p>'
        );
        
        $this->assertResult(
'<div>
Par1
</div>', true
        );
        
        $this->assertResult(
'<div>Par1

<div>Par2</div></div>',
'<div><p>Par1</p><div>Par2</div></div>'
        );
        
        $this->assertResult(
'<div>Par1
<div>Par2</div></div>',
'<div><p>Par1
</p><div>Par2</div></div>'
        );
        
        $this->assertResult(
'Par1
<div>Par2</div>',
'<p>Par1
</p><div>Par2</div>'
        );
        
        $this->assertResult(
'Par

Par2',
            true,
            array('AutoFormat.AutoParagraph' => true, 'HTML.Parent' => 'span')
        );
        
    }
    
    function testLinkify() {
        
        $this->config = array('AutoFormat.Linkify' => true);
        
        $this->assertResult(
            'http://example.com',
            '<a href="http://example.com">http://example.com</a>'
        );
        
        $this->assertResult(
            '<b>http://example.com</b>',
            '<b><a href="http://example.com">http://example.com</a></b>'
        );
        
        $this->assertResult(
            'This URL http://example.com is what you need',
            'This URL <a href="http://example.com">http://example.com</a> is what you need'
        );
        
        $this->assertResult(
            '<a>http://example.com/</a>'
        );
        
    }
    
    function testMultipleInjectors() {
        
        $this->config = array('AutoFormat.AutoParagraph' => true, 'AutoFormat.Linkify' => true);
        
        $this->assertResult(
            'Foobar',
            '<p>Foobar</p>'
        );
        
        $this->assertResult(
            'http://example.com',
            '<p><a href="http://example.com">http://example.com</a></p>'
        );
        
        $this->assertResult(
            '<b>http://example.com</b>',
            '<p><b><a href="http://example.com">http://example.com</a></b></p>'
        );
        
        $this->assertResult(
            '<b>http://example.com',
            '<p><b><a href="http://example.com">http://example.com</a></b></p>'
        );
        
        $this->assertResult(
'http://example.com

http://dev.example.com',
            '<p><a href="http://example.com">http://example.com</a></p><p><a href="http://dev.example.com">http://dev.example.com</a></p>'
        );
        
        $this->assertResult(
            'http://example.com <div>http://example.com</div>',
            '<p><a href="http://example.com">http://example.com</a> </p><div><a href="http://example.com">http://example.com</a></div>'
        );
        
        $this->assertResult(
            'This URL http://example.com is what you need',
            '<p>This URL <a href="http://example.com">http://example.com</a> is what you need</p>'
        );
        
    }
    
}

?>