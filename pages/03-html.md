# Test HTML

We test here all available HTML features.

<h1>Test</h1>
<hgroup>
    <h1>Heading 1</h1>
    <h2>Heading 2</h2>
    <h3>Heading 3</h3>
    <h4>Heading 4</h4>
    <h5>Heading 5</h5>
    <h6>Heading 6</h6>
</hgroup>
<p>
    <a>a-nohref</a>
    <a href="http://example.com">a-href</a>
    <a class="external" href="http://example.com">a-href</a>
    <a class="safe" href="http://example.com">a-href</a>
    <a class="unsafe" href="http://example.com">a-href</a>
    <abbr title="abbreviation">abbr</abbr>
    <b>b</b>
    <bdi>bdi</bdi>
    <bdo dir="rtl">bdo</bdo>
    <cite>cite</cite>
    <code>$n = $x ? $a : $b</code>
    <data value="8593539033981" title="Koh-i-noor Hardtmuth Kombinovaná stěrací pryž">data</data>
    <del>del</del><ins>ins</ins>
    <dfn>dfn</dfn>
    <em>em</em>
    <i>i</i>
    <kbd>kbd</kbd>
    <mark>mark</mark>
    <meter value="42" min="0" low="25" optimum="40" high="80" max="100">meter</meter>
    <progress value="64" max="110">progress</progress>
    <q>q</q>
    <s>s</s>
    <samp>samp</samp>
    <small>small</small>
    <strong>strong</strong>
    x<sub>sub</sub>
    x<sup>sup</sup>
    <u>u</u>
    <var>var</var>
    <time datetime="2026-12-31 12:34:56">2026-12-31 12:34:56</time>
    Limonadenst&auml;nde<wbr>bau<wbr>privat<wbr>finanzierugs<wbr>gesetz
    Limonadenst&auml;nde&shy;bau&shy;privat&shy;finanzierugs&shy;gesetz
</p>
<fieldset>
   <legend>Legend for fieldset</legend>
   <p>Fieldset content</p>
</fieldset>
<h3>Address</h3>
<address>
    Contact:<br>
    <a href="mailto:joe@limonada.cz">joe@limonada.cz</a><br>
    <a href="tel:+420602123456">+420 602 123 456</a>
</address>
<blockquote cite="http://example.com">
    Blockquote without paragraph element.
</blockquote>
<blockquote cite="http://example.com">
    <p>Blockquote with paragraph element.</p>
</blockquote>
<dl>
    <dt>DataList Term</dt>
    <dd>DataList Term Description</dd>
</dl>
<details>
    <summary>Open/Close Details</summary>
    Details content (no paragraph element)
</details>
<details>
    <summary>Open/Close Details</summary>
    <p<Details content (wrapped with paragraph element)</p>
</details>
<h3>Lists</h3>
<ul>
    <li>UL-1</li>
    <li>UL-2
        <ul>
            <li>UL-2.1</li>
            <li>UL-2.2</li>
        </ul>
    </li>
    <li>UL-3</li>
</ul>
<ol>
    <li>OL-1</li>
    <li>OL-2
        <ol>
            <li>OL-2.1</li>
            <li>OL-2.2</li>
        </ol>
    </li>
    <li>OL-3</li>
</ol>
<menu>
    <li>MENU-1</li>
    <li>MENU-2
        <menu>
            <li>MENU-2.1</li>
            <li>MENU-2.2</li>
        </menu>
    </li>
    <li>MENU-3</li>
</menu>
<pre>PRE. Pohanime vase sny.</pre>
<ruby> 漢 <rp>(</rp>
    <rt>kan</rt>
    <rp>)</rp> 字 <rp>(</rp>
    <rt>ji</rt>
    <rp>)</rp>
</ruby>
<table>
    <caption>This is a table</caption>
    <colgroup>
        <col />
        <col span="2" class="someclass">
    </colgroup>
    <thead>
        <tr>
            <th scope="col">Col 1</th>
            <th scope="col">Col 2.1</th>
            <th scope="col">Col 2.2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th scope="row">Hello</th>
            <td>World</td>
            <td>Universe</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th scope="row">Total</th>
            <td colspan="2">1&nbsp;234,56 Kc</td>
        </tr>
    </tfoot>
</table>
<map name="mapka">
    <area shape="rect" coords="30,30,50,50" href="http://example/rect.html">
    <area shape="circle" coords="80,80,50" href="http://example/circle.html">
    <area shape="poly" coords="30,30,50,50,30,50,50,30" href="http://example/poly.html">
</map>
<img usemap="#mapka"
    src="https://images.unsplash.com/photo-1717343824623-06293a62a70d?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjR8fG1hcHxlbnwwfHwwfHx8MA%3D%3D"
    alt="Image for map 'mapka'">
<figure>
    <figcaption>Figcaption Figure Img</figcaption>
    <img src="https://images.unsplash.com/photo-1771241222039-facdf3435d73?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHwyN3x8fGVufDB8fHx8fA%3D%3D"
        alt="Image in figure element">
</figure>
<img src="https://images.unsplash.com/photo-1771241222039-facdf3435d73?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHwyN3x8fGVufDB8fHx8fA%3D%3D"
alt="Independent image">
<picture>
    <!-- <source>
    <source> -->
    <img src="https://images.unsplash.com/photo-1771241222039-facdf3435d73?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHwyN3x8fGVufDB8fHx8fA%3D%3D"
    alt="Image in picture element">
</picture>
