<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:t="http://www.tei-c.org/ns/1.0"
    exclude-result-prefixes="t"
    version="2.0">

    <!--
      Project-specific override over official EpiDoc start-edition stylesheet.
      We keep official rendering as baseline and only override elements needed
      for Leiden/Zaliznyak toggle semantics in the frontend.
    -->
    <xsl:import href="../epidoc-stylesheets/start-edition.xsl"/>

    <xsl:template match="t:supplied[@reason='lost']">
        <span class="epidoc-supplied epidoc-supplied--lost" data-supplied-reason="lost">
            <xsl:apply-templates/>
        </span>
    </xsl:template>

    <xsl:template match="t:supplied[@reason='unclear']">
        <span class="epidoc-supplied epidoc-supplied--unclear" data-supplied-reason="unclear">
            <xsl:call-template name="epigraphy-render-underdotted-spans">
                <xsl:with-param name="text" select="string(.)"/>
            </xsl:call-template>
        </span>
    </xsl:template>

    <xsl:template match="t:unclear">
        <span class="epidoc-unclear" data-epidoc-hook="unclear">
            <xsl:call-template name="epigraphy-render-underdotted-spans">
                <xsl:with-param name="text" select="string(.)"/>
            </xsl:call-template>
        </span>
    </xsl:template>

    <xsl:template name="epigraphy-render-underdotted-spans">
        <xsl:param name="text"/>
        <xsl:variable name="text-normalized" select="normalize-space($text)"/>
        <xsl:for-each select="1 to string-length($text-normalized)">
            <xsl:variable name="ch" select="substring($text-normalized, ., 1)"/>
            <xsl:choose>
                <xsl:when test="$ch = ' '">
                    <xsl:text> </xsl:text>
                </xsl:when>
                <xsl:otherwise>
                    <span class="epidoc-underdot-char">
                        <xsl:value-of select="$ch"/>
                    </span>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>

</xsl:stylesheet>
