W render engine scheme
======================

This diagram represent W rendering chain.


```mermaid
flowchart TD

    0A(Head generation) -->
    0rss(RSS feed declaration) --> 3B

    2A[[Body]] -->
    2B(W inclusion)  -------->
    2C((Elements inclusion)) -->
    2D(Summary) -->
    2rss(RSS detection) --> 2I
    subgraph "DOM parser"
        2I(Link and media analysis) -->
        url(URL checker) -->
        2K(comment form parser)
    end
    2K --> 2pp
    2pp(check for post render actions) -->
    3B((Head and Body gathering)) -->
    3C[[Rendered HTML]] --> 4c
    subgraph "post render actions"
        4c(counters) -->
        4e(disable comment inputs) -->
        4j(include js vars)
    end
    4j --> 5{served web page}


    1A[[Element]] -->
    1B(W inclusion) -->
    1C(wiki links) -->
    1D(every link*) -->
    1E(Markdown) --> 1F
    subgraph "post MD parser"
        1F(header ID) -->
        1G(URL linker) -->
        typo(Typography fixer) -->
        1H(HTML tag*)
    end
    1H --> 2C

    1E -. "send TOC structure" .-> 2D
    2rss -. "send rss links" .-> 0rss
    2pp -. trigger post render action .-> 4c
    2K -. trigger post render action .-> 4e

    urlcache@{ shape: lin-cyl, label: "URL cache" }
    cloud@{ shape: cloud, label: "the Web"}
    urlcache <-.-> url
    cloud <-.-> urlcache

```

- *every link: rendering option that transform every word as a link
- *HTML tag: [rendering option](MANUAL.md#html-tags) that does not print Element's corresponding HTML tags (only for pages V1)




## W inclusions

List of W inclusions

1. replace `%DATE%`, `%DATEMODIF%`, `%TIME%`, `%TIMEMODIF%` codes
1. replace `%THUMBNAIL%` code
1. replace `%PAGEID%` and `%ID%` code
1. replace `%URL%` code
1. replace `%PATH%` code
1. replace `%TITLE%` code
1. replace `%DESCRIPTION%` code
1. replace `%LIST%` code
1. replace `%MEDIA%` code
1. replace `%MAP%` code
1. replace `%RANDOM%` code
1. replace `%AUTHORS%` code
1. replace `%CONNECT%` code
1. replace `%COMMENTS%` code
1. replace `%COMMENTCOUNT%` code

The point of doing those inclusions early is to be before __Header ID__ parser. That way, when they are used inside HTML headings, they will generate nicer IDs.
