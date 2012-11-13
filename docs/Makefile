#
# Compile documents for SWE3613 into something useful. 
#
# Lee Hall 
# 	-Thu Oct 18 23:51:32 EDT 2012

PDFTK=/usr/bin/pdftk
PDFLATEX=/usr/bin/pdflatex
MBOX2PDF=/usr/local/bin/mbox2pdf.pl

OUTPUT=Group4_Final_Report.pdf
BODY=Report_Body.pdf
COMPILED_APPENDICES=Source.pdf Status_Reports.pdf Minutes.pdf \
	Commit_Logs.pdf Slides.pdf
	
EXTERNAL_APPENDICES=Email.pdf 
APPENDICES=$(COMPILED_APPENDICES) $(EXTERNAL_APPENDICES)


all: $(BODY) $(APPENDICES)
	$(PDFTK) $(BODY) $(COMPILED_APPENDICES) cat output $(OUTPUT)
	
Source.pdf: APP_SEC=A
Source.pdf: Source
	cd $<; \
		$(PDFLATEX) $<.tex	
	@echo; echo; echo;
	@echo "+++ There are no code listings for the following files:"
	find ../html ../bin -iname \*php -o -iname \*py |\
		while read FN; do\
			grep "$$FN" Source/Source.tex || \
				echo "\lstinputlisting{../$$FN}"; \
		done; \

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

# Uncompiled, need to create File.
Email.pdf: APP_SEC=C
Email.pdf: Email 
	cd $<; \
	$(MBOX2PDF) email.mbox tmp_dir; \
	cp tmp_dir/$@ . ;\
	rm -rf tmp_dir	
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/pdf_tmpl.tex > $</$<.tex 
	cd $<; \
		$(PDFLATEX) $<.tex; \
		rm $<_list.pdf; \
		mv $@ .. 
	cp $@ Appendix-$(APP_SEC)_$@

Minutes.pdf: APP_SEC=D
Minutes.pdf: Minutes templates/pdf_tmpl.tex
	$(PDFTK) $(wildcard $</*.pdf) cat output \
		$</$<_list.pdf 
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/pdf_tmpl.tex > $</$<.tex 
	cd $<; \
		$(PDFLATEX) $<.tex; \
		rm $<_list.pdf; \
		mv $@ .. 

Commit_Logs.pdf: APP_SEC=E
Commit_Logs.pdf: Commit_Logs templates/txt_tmpl.tex
	git log > $</$<_list.txt
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/txt_tmpl.tex > $</$<.tex 
	cd $<; \
		$(PDFLATEX) $<.tex; \
		rm $<_list.txt; \
		mv $@ .. 

Slides.pdf: APP_SEC=F
Slides.pdf: Slides 
	sed 's/<<APP_SEC>>/$(APP_SEC)/g;s/<<TITLE>>/$</g' \
		templates/pdf_tmpl.tex > $</$<.tex 
	cd $<; \
		$(PDFLATEX) $<.tex; \
		mv $@ .. 
