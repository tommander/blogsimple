# Test

We test here all available formatting features.

## 1 Inline formatting

### Markdown

This **bold** text is __bold__\*, but sometimes *italic* _too_\*\*, so ~~scratch~~ ~all~ and ***drink*** `water` from [example.com](http://example.com) then go to [next section](#2-block-formatting).

### HTML

<p><a>a-nohref</a><a href="http://example.com">a-href</a><a class="external" href="http://example.com">a-href</a><a class="safe" href="http://example.com">a-href</a><a class="unsafe" href="http://example.com">a-href</a><abbr title="abbreviation">abbr</abbr><b>b</b><bdi>bdi</bdi><bdo dir="rtl">bdo</bdo><cite>cite</cite><code>$n = $x ? $a : $b</code><data value="8593539033981" title="Koh-i-noor Hardtmuth Kombinovaná stěrací pryž">data</data><del>del</del><ins>ins</ins><dfn>dfn</dfn><em>em</em><i>i</i><kbd>kbd</kbd><mark>mark</mark><meter value="42" min="0" low="25" optimum="40" high="80" max="100">meter</meter><progress value="64" max="110">progress</progress><q>q</q><s>s</s><samp>samp</samp><small>small</small><strong>strong</strong>x<sub>sub</sub>x<sup>sup</sup><u>u</u><var>var</var><time datetime="2026-12-31 12:34:56">2026-12-31 12:34:56</time>Limonadenst&auml;nde<wbr>bau<wbr>privat<wbr>finanzierugs<wbr>gesetzLimonadenst&auml;nde&shy;bau&shy;privat&shy;finanzierugs&shy;gesetz</p>

## 2 Block formatting

### 2.1 Headings

#### Markdown

# Heading 1
## Heading 2
### Heading 3
#### Heading 4
##### Heading 5
###### Heading 6

#### HTML

<hgroup>
<h1>Heading 1</h1>
<h2>Heading 2</h2>
<h3>Heading 3</h3>
<h4>Heading 4</h4>
<h5>Heading 5</h5>
<h6>Heading 6</h6>
</hgroup>

### 2.2 Quote

#### Markdown

> Quote

#### HTML

<blockquote cite="http://example.com">
Blockquote without paragraph element.
</blockquote>
<blockquote cite="http://example.com">
<p>Blockquote with paragraph element.</p>
</blockquote>

### 2.3 Code

#### Markdown

```magic
bibbidi
bobbidi
boo
```

#### HTML

<pre>PRE. Pohanime vase sny.</pre>

### 2.4 Line breaks

Two Spaces  
Slash\
BR no backslash<br>
BR with backslash<br/>
Last line just for fun

### 2.5 Lists

#### Markdown

* UL-1
* UL-2
   * UL-2.1
   * UL-2.2
* UL-3

1. OL-1
1. OL-2
   1. OL-2.1
   1. OL-2.2
1. OL-3

* UL-1
* UL-2
   1. OL-2.1
   1. OL-2.2
* UL-3

1. OL-1
1. OL-2
   * UL-2.1
   * UL-2.2
1. OL-3

- [x] Task Complete
- [ ] Task Incomplete

#### HTML

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

### 2.6 Images

#### Markdown (+html wrapper)

![Sunflower by Todd Trapani@unsplash.](images/lavender.webp)

<div class="imgleft">

![Sunflower by Todd Trapani@unsplash.](images/lavender.webp)

</div>
<div class="imgright">

![Sunflower by Todd Trapani@unsplash.](images/lavender.webp)

</div>
<div class="imgcenter">

![Sunflower by Todd Trapani@unsplash.](images/lavender.webp)

</div>

#### HTML

<p><img src="images/lavender.webp" alt="Independent image"></p>
<div class="imgleft">

![Sunflower by Todd Trapani@unsplash.](images/lavender.webp)

</div>
<div class="imgright">

![Sunflower by Todd Trapani@unsplash.](images/lavender.webp)

</div>
<div class="imgcenter">

![Sunflower by Todd Trapani@unsplash.](images/lavender.webp)

</div>
<figure>
<figcaption>Figcaption Figure Img</figcaption>
<img src="images/lavender.webp" alt="Image in figure element">
</figure>
<picture>
<!-- <source> -->
<img src="images/lavender.webp" alt="Image in picture element">
</picture>

### 2.7 Tables

#### Markdown

Column header is aligned based on whitespaces,
normal cell is aligned based on the colon in header separator.

|Col1 | Col2 |Col 3 |
|-----:|:------|:------:|
|ABCD 123|ABCD 123|ABCD 123|
| ABCD 1| ABCD 1| ABCD 1|
| ABCD 2 | ABCD 2 | ABCD 2 |
|ABCD 3 |ABCD 3 |ABCD 3 |

#### HTML

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
<td colspan="2">EUR 1,234.56</td>
</tr>
</tfoot>
</table>

### 2.9 HTML-specific features

<fieldset>
<legend>Legend for fieldset</legend>
<p>Fieldset content</p>
</fieldset>
<address>
Contact:<br>
<a href="mailto:joe@lemonade.org">joe@lemonade.org</a><br>
<a href="tel:+12345678900">+1 (234) 567-8900</a>
</address>
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
<p>Details content (wrapped with paragraph element)</p>
</details>
<ruby> 漢 <rp>(</rp>
<rt>kan</rt>
<rp>)</rp> 字 <rp>(</rp>
<rt>ji</rt>
<rp>)</rp>
</ruby>
<p>Map with are</p>
<map name="mappy-mcmapface">
    <area shape="rect" coords="30,30,50,50" href="http://example.com/rect.html">
    <area shape="circle" coords="80,80,50" href="http://example.com/circle.html">
    <area shape="poly" coords="30,30,50,50,30,50,50,30" href="http://example.com/poly.html">
</map>
<img usemap="#mappy-mcmapface"
    src="http://blog.example.com/images/lavender.webp"
    alt="Image for map 'mappy-mcmapface'">
