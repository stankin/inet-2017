<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/group">
		<html>
		<head>
		<link href="style.css" rel="stylesheet" type="text/css" />
		</head>
		<body>
		<div class = "header">
			<span id = "pheader">Группа ИДМ-17-01</span>
		</div>
		<table border = '1'>
			<tr>
				<th>Студент</th>
				<th>Модуль 1</th>
				<th>Модуль 2</th>
				<th>Страница</th>
				<th>Команда</th>
				<th>Роль</th>
			</tr>
			<xsl:for-each select="user">
			<tr>
				<td><xsl:value-of select="name" /></td>
				<td><xsl:value-of select="m1" /></td>
				<td><xsl:value-of select="m2" /></td>
				<td>
					<a>
						<xsl:attribute name="href">
							<xsl:value-of select="'https://stankin.github.io/inet-2017/idm-17-01/'" />
							<xsl:value-of select="page" />
						</xsl:attribute>
					</a>
				</td>
				<td><xsl:value-of select="team" /></td>
				<td><xsl:value-of select="role" /></td>
			</tr>
			</xsl:for-each>
		</table>
		</body>
		</html>
	</xsl:template>


</xsl:stylesheet>