<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/group">
		<html>
		<body>
			<xsl:for-each select="user">
				<xsl:value-of select="name" /><br />
				<xsl:value-of select="m1" /><br />
				<xsl:value-of select="m2" /><br />
				<xsl:value-of select="page" /><br />
				<xsl:value-of select="team" /><br />
				<xsl:value-of select="role" /><br />
			</xsl:for-each>
		</body>
		</html>
	</xsl:template>


</xsl:stylesheet>