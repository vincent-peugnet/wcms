#!/bin/sh
pandoc -c manual.css --toc=true --metadata title="W manual $(cat VERSION)" --pdf-engine=weasyprint -o manual.pdf MANUAL.md
