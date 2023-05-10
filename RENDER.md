W render engine scheme
======================

This diagram represent W rendering chain.


```mermaid
flowchart TD

    0A(Head generation) -->
    0rss(RSS feed declaration) --> 3B

    2A[[Body]] -->
    2B(W inclusion 1)  ------->
    2C((Element inclusion)) --> 2D
    subgraph "post inclusion parser"
        2D(Summary) -->
        2rss(RSS detection) -->
        2F(W inclusion 2) -->
        2H(Wiki links) -->
        2I(Link and media analysis)
    end
    2I -->
    3B((Head and Body gathering)) -->
    3C[[Rendered HTML]] --> 4c
    subgraph "post render actions"
        4c(counters) -->
        4j(js vars)
    end
    4j --> 5[\served web page/]


    1A[[Element]] -->
    1B(W inclusion 1) -->
    1C(every link*) -->
    1D(Markdown) --> 1E
    subgraph "post MD parser"
        1E(header ID) -->
        1F(URL linker) -->
        1G(HTML tag*)
    end
    1G --> 2C

    1E -. "send TOC structure" .-> 2D
    2rss -. "send rss links" .-> 0rss
```

- *every link: rendering option that transform every word as a link
- *HTML tag: rendering option that does not print Element's corresponding HTML tags




## W inclusions 1

List of W inclusions part 1

1. replace `%DATE%`, `%DATEMODIF%`, `%TIME%`, `%TIMEMODIF%` codes
1. replace `%THUMBNAIL%` code
1. replace `%PAGEID%` and `%ID%` code
1. replace `%URL%` code
1. replace `%PATH%` code
1. replace `%TITLE%` code
1. replace `%DESCRIPTION%` code

The point of doing those inclusions early is to be before __Header ID__ parser. That way, when they are used inside HTML headings, they will generate nicer IDs.

## W inclusions 2

List of inclusions part 2

1. replace `%LIST%` code
1. replace `%MEDIA%` code
1. replace `%MAP%` code
1. replace `%RANDOM%` code
1. replace `%AUTHORS%` code
1. replace `%CONNECT%` code
