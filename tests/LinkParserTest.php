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

    public function testLinkInAnchorTagsDoubleQ() {
        $hasString = '<a href="http://www.222.com">Resources and Guides</a>';
        $expected = 'http://www.222.com';
        $actual = LinkParser::find_links($hasString);

        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));
    }

    //Valid html can wrap links with single quotes, and this test fails to pass.
    public function testLinkInAnchorTagsSingleQ() {
        $hasString = "<a href='http://www.222.com'>Resources and Guides</a>";
        $expected = 'http://www.222.com';

        $actual = LinkParser::find_links($hasString);
        Debug::show($actual);
        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));
    }

    public function testHttpsLink() {
        $hasString = 'https://www.222.com';
        $expected = 'https://www.222.com';
        $actual = LinkParser::find_links($hasString);

        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));
    }

    //links in caps should still be recognised
    public function testCaps() {
        $hasString = 'HTTP://WWW.222.COM';
        $expected = 'HTTP://WWW.222.COM';
        $actual = LinkParser::find_links($hasString);
        Debug::show($actual);
        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));
    }

    public function testBackwardsBracket() {
        $hasString = 'https:\\www.222.com';
        $expected = 'http://www.222.com';
        $actual = LinkParser::find_links($hasString);

        $this->assertNotContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));
    }

    public function testInvalidLinkSymbols() {
        $hasString = 'https://@www.222.com!';
        $expected = 'http://www.222.com';
        $actual = LinkParser::find_links($hasString);

        $this->assertNotContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));
    }

    public function testLengthyDomain() {

        $hasString = 'http://www.llanfairpwllgwyngyllgogerychwyrndrobwllllantysiliogogogochuchaf.com';
        $expected = 'http://www.llanfairpwllgwyngyllgogerychwyrndrobwllllantysiliogogogochuchaf.com';
        $actual = LinkParser::find_links($hasString);

        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));

    }

    public function testNet() {

        $hasString = 'http://www.osimerithrerg3.net';
        $expected = 'http://www.osimerithrerg3.net';
        $actual = LinkParser::find_links($hasString);

        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));

    }

    public function testOrg() {

        $hasString = 'http://www.osimerithrerg3.org';
        $expected = 'http://www.osimerithrerg3.org';
        $actual = LinkParser::find_links($hasString);

        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));

    }

    public function testInfo() {

        $hasString = 'http://www.osimerithrerg3.info';
        $expected = 'http://www.osimerithrerg3.info';
        $actual = LinkParser::find_links($hasString);

        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));

    }

    public function testCoNz() {

        $hasString = 'http://www.osimerithrerg3.co.nz';
        $expected = 'http://www.osimerithrerg3.co.nz';
        $actual = LinkParser::find_links($hasString);

        $this->assertContains($expected, $actual, 'Actual link did not match expected.');

        $this->assertEquals(1, count($actual));

    }


}