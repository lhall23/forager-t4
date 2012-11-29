#
# Compile documents for SWE3613 into something useful. 
#
# Lee Hall 
# 	-Thu Oct 18 23:51:32 EDT 2012

PDFTK=/usr/bin/pdftk
PDFLATEX=/usr/bin/pdflatex
XPS2PDF=/usr/bin/xpstopdf
MBOX2PDF=/usr/local/bin/mbox2pdf.pl

OUTPUT=Group4_Final_Report.pdf
BODY=Report_Body.pdf
COMPILED_APPENDICES=Slides.pdf Status_Reports.pdf Minutes.pdf \
	Source.pdf Commit_Logs.pdf 
	
EXTERNAL_APPENDICES=Email.pdf 
APPENDICES=$(COMPILED_APPENDICES) $(EXTERNAL_APPENDICES)


all: $(BODY) $(APPENDICES)
	$(PDFTK) $(BODY) $(COMPILED_APPENDICES) cat output $(OUTPUT)
	
Slides.pdf: APP_SEC=A
Slides.pdf: Slides 
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/slides_tmpl.tex > $</$<.tex 
	cd $<; \
		$(PDFLATEX) $<.tex; \
		mv $@ .. 

Status_Reports.pdf: APP_SEC=B
Status_Reports.pdf: Status_Reports templates/pdf_tmpl.tex
	$(PDFTK) $(wildcard $</*.pdf) cat output \
		$</$<_list.pdf 
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/pdf_tmpl.tex > $</$<.tex 
	cd $<; \
		$(PDFLATEX) $<.tex; \
		rm $<_list.pdf; \
		mv $@ .. 

Minutes.pdf: APP_SEC=C
Minutes.pdf: Minutes templates/pdf_tmpl.tex
	$(PDFTK) $(wildcard $</*.pdf) cat output \
		$</$<_list.pdf 
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/pdf_tmpl.tex > $</$<.tex 
	cd $<; \
		$(PDFLATEX) $<.tex; \
		rm $<_list.pdf; \
		mv $@ .. 

# Uncompiled, need to create File.
Email.pdf: APP_SEC=D
Email.pdf: Email 
	cd $<; \
	#$(MBOX2PDF) email.mbox tmp_dir; \
	cp tmp_dir/$@ . ;\
	rm -rf tmp_dir	
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/pdf_tmpl.tex > $</$<.tex 
	cd $<; \
		$(XPS2PDF) email.xps $<_list.pdf; \
		$(PDFLATEX) $<.tex; \
		rm $<_list.pdf; \
		mv $@ .. 
	mv $@ Appendix-$(APP_SEC)_$@

Source.pdf: APP_SEC=E
Source.pdf: Source
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/source_tmpl.tex > $</$<.tex 
	cd $<; \
		$(PDFLATEX) $<.tex;
	mv $</$<.pdf .
	@echo; echo; echo;
	@echo "+++ There are no code listings for the following files:"
	find ../html ../bin -iname \*php -o -iname \*py |\
		while read FN; do\
			grep -q "$$FN" Source/Source.tex || \
				echo "\lstinputlisting{../$$FN}"; \
		done; \


Commit_Logs.pdf: APP_SEC=F
Commit_Logs.pdf: Commit_Logs templates/txt_tmpl.tex
	git log > $</$<_list.txt
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/txt_tmpl.tex > $</$<.tex 
	cd $<; \
		$(PDFLATEX) $<.tex; \
		rm $<_list.txt; \
		mv $@ .. 
