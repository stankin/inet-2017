<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output media-type="application/html"/>
	<xsl:template match="/exam">
		<html>
		<head>
		<link href="style.css" rel="stylesheet" type="text/css" />
		</head>
		<body>
		<div class = "header">
			<span id = "pheader">Вопросы группы ИДМ-17-01</span>
		</div>
		<div class = "qbody">
			<xsl:for-each select="question">
				<div class="sq">
					<div class = "aname">
						Автор:<br></br> 
						<a>
							<xsl:attribute name="href"><xsl:value-of select="plink" />	
							</xsl:attribute>
							<xsl:value-of select="author" />
						</a>
					</div>
					<hr></hr>
					<div class = "qtext">
						<b>Вопрос:</b><br></br> <xsl:copy-of select="qtext" />
					</div>
					<hr></hr>
					<div class = "ranswer">
						<b>Правильный ответ:</b> <xsl:copy-of select="ranswer" />
					</div>
					<hr></hr>
					<xsl:for-each select="answer">
						<div class = "answer">
							<b>Неверный вариант ответа:</b> <xsl:copy-of select="." />
						</div>
					</xsl:for-each>
					<hr></hr>
					<div class = "acomment">
						<b>Комментарий автора:</b><br></br> <xsl:copy-of select="acomment" />
					</div>
					<hr></hr>
					<div class = "proofrlink">
						<a>
							<xsl:attribute name="href"><xsl:value-of select="proofrlink" />
							</xsl:attribute>
							Ссылка на пруф
						</a>
					</div>
				</div>
			</xsl:for-each>
		</div>	
	</body>
	</html>
	</xsl:template>


</xsl:stylesheet>
