<?php

class LinkParserTest extends SapphireTest {


    public function testBareLink() {

        $hasString = 'http://www.osimerithrerg3.com';
        $expected = 'http://www.osimerithrerg3.com';
        $actual = LinkParser::find_links($hasString);

        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));

    }

    public function testLinkInContent() {

        $hasString = 'Sohaeofdih weofihweofihweof oihweowe http://www.222.com wefiuhwf';
        $expected = 'http://www.222.com';
        $actual = LinkParser::find_links($hasString);

        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));

    }
}