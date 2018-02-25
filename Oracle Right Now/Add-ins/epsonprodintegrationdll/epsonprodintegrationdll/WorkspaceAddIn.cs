using System;
using System.AddIn;
using System.Drawing;
using System.IO;
using System.Net;
using System.Net.Security;
using System.Windows.Forms;
using System.Xml;
using RightNow.AddIns.AddInViews;
using RightNow.AddIns.Common;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Text;
using System.Web.Script.Serialization;
using System.Diagnostics;

////////////////////////////////////////////////////////////////////////////////
//
// File: WorkspaceAddIn.cs
//
// Comments:
//
// Notes: 
//
// Pre-Conditions: 
//
////////////////////////////////////////////////////////////////////////////////
namespace epsonprodintegrationdll
{
    public class WorkspaceAddIn : Panel, IWorkspaceComponent2
    {
        /// <summary>
        /// The current workspace record context.
        /// </summary>
        private static IRecordContext _recordContext;
        private static IGlobalContext _globalContext;
        private static int agentid;
        private static string extns;
        private static bool response;

        /// <summary>
        /// Default constructor.
        /// </summary>
        /// <param name="inDesignMode">Flag which indicates if the control is being drawn on the Workspace Designer. (Use this flag to determine if code should perform any logic on the workspace record)</param>
        /// <param name="RecordContext">The current workspace record context.</param>
        public WorkspaceAddIn(bool inDesignMode, IRecordContext RecordContext, IGlobalContext GlobalContext)
        {
            if (!inDesignMode)
            {
                try
                {
                    _globalContext = GlobalContext;
                    _recordContext = RecordContext;
                    agentid = _globalContext.AccountId;
                    //MessageBox.Show("this is constructor");
                    extns = "";
                    response = false;
                }
                catch (Exception e)
                {
                    MessageBox.Show(e.Message);
                }
            }
        }

        #region IAddInControl Members

        /// <summary>
        /// Method called by the Add-In framework to retrieve the control.
        /// </summary>
        /// <returns>The control, typically 'this'.</returns>
        public Control GetControl()
        {
            return this;
        }

        #endregion

        #region IWorkspaceComponent2 Members

        /// <summary>
        /// Sets the ReadOnly property of this control.
        /// </summary>
        public bool ReadOnly { get; set; }

        /// <summary>
        /// Method which is called when any Workspace Rule Action is invoked.
        /// </summary>
        /// <param name="ActionName">The name of the Workspace Rule Action that was invoked.</param>
        public void RuleActionInvoked(string ActionName)
        {
            //_globalContext.LogMessage("entering");
            if (ActionName == "stop")
            {
                //MessageBox.Show("entering the credit card triggering");
                //_globalContext.LogMessage("entering the credit card triggering");
                displaymsg("Stop");
            }
            if (ActionName == "start")
            {
                //_globalContext.LogMessage("leaving the credit card triggering");
                displaymsg("Start");
            }
        }

        /// <summary>
        /// Method which is called when any Workspace Rule Condition is invoked.
        /// </summary>
        /// <param name="ConditionName">The name of the Workspace Rule Condition that was invoked.</param>
        /// <returns>The result of the condition.</returns>
        public string RuleConditionInvoked(string ConditionName)
        {
            if (response)
            {
                response = false;
                return "True";
            }
            return "false";
        }

        public static void displaymsg(string action)
        {
            System.Diagnostics.Stopwatch timer = new Stopwatch();
            if (extns.Length == 0)
            {
                extns = getExtension(agentid);
            }
            ServicePointManager.ServerCertificateValidationCallback = new RemoteCertificateValidationCallback(delegate { return true; });
            string url = "https://rntp.goepson.com/mitel/XISOAPAdapter/MessageServlet?senderParty=&senderService=BS_NA_3PRightnow_P&receiverParty=&receiverService=&interface=Agent_Async_Out&interfaceNamespace=http://SVM/Mitel/CallRecording";
            //string url = "https://rntn.goepson.com/mitel/XISOAPAdapter/MessageServlet?senderParty=&senderService=BS_NA_3PRightnow_Q&receiverParty=&receiverService=&interface=Agent_Async_Out&interfaceNamespace=http://SVM/Mitel/CallRecording";
            //string url = "https://rntn.goepson.com/mitel/XISOAPAdapter/MessageServlet?senderParty=&senderService=BS_NA_3PRIGHTNOW_D&receiverParty=&receiverService=&interface=Agent_Async_Out&interfaceNamespace=http://SVM/Mitel/CallRecording";
            //string url = "https://rntn.goepson.com/tst/XISOAPAdapter/MessageServlet?senderParty=&senderService=BS_NA_3PRIGHTNOW_D&receiverParty=&receiverService=&interface=Agent_Async_Out&interfaceNamespace=http://SVM/Mitel/CallRecording";
            XmlDocument soapEnvelopeXml = new XmlDocument();
            soapEnvelopeXml.LoadXml(@"<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:cal='http://SVM/Mitel/CallRecording'><soapenv:Header/><soapenv:Body><cal:MT_Agent><Agent><AgentID>" + extns + "</AgentID><Action>" + action + "</Action></Agent></cal:MT_Agent></soapenv:Body></soapenv:Envelope>");
            try
            {
                HttpWebRequest webRequest = (HttpWebRequest)WebRequest.Create(url);
                //webRequest.Headers.Add("SOAPAction", action);

                //String encoded = System.Convert.ToBase64String(System.Text.Encoding.GetEncoding("ISO-8859-1").GetBytes("RNT_PO_MITEL:Epson@123"));
                String encoded = System.Convert.ToBase64String(System.Text.Encoding.GetEncoding("ISO-8859-1").GetBytes("RNT_PO_MITEL:Epson$2017"));
                //String encoded = System.Convert.ToBase64String(System.Text.Encoding.GetEncoding("ISO-8859-1").GetBytes("RNT_PO_MITEL:Wrep5$P0R"));

                webRequest.Headers.Add("Authorization", "Basic " + encoded);
                webRequest.ContentType = "text/xml;charset=\"utf-8\"";
                webRequest.Accept = "text/xml";
                webRequest.Method = "POST";

                timer.Start();
                using (Stream stream = webRequest.GetRequestStream())  
                {
                    soapEnvelopeXml.Save(stream);
                }


                // begin async call to web request.
                IAsyncResult asyncResult = webRequest.BeginGetResponse(null, null);

                // suspend this thread until call is complete. You might want to
                // do something usefull here like update your UI.
                asyncResult.AsyncWaitHandle.WaitOne();

                // get the response from the completed web request.
                string soapResult;


                using (WebResponse webResponse = webRequest.EndGetResponse(asyncResult))
                {
                    //_globalContext.LogMessage("before triggering");
                    //_recordContext.TriggerNamedEvent("response");
                    //_globalContext.LogMessage("After triggering");
                    //Thread.Sleep(3000);
                    response = true;
                    using (StreamReader rd = new StreamReader(webResponse.GetResponseStream()))
                    {
                        soapResult = rd.ReadToEnd();
                    }
                    timer.Stop();
                }
                //TimeSpan timeTaken = timer.Elapsed;
                //string elapsedTime = String.Format("{0:00}:{1:00}:{2:00}.{3:00}",timeTaken.Hours, timeTaken.Minutes, timeTaken.Seconds,timeTaken.Milliseconds / 10);
                //_globalContext.LogMessage("elapsed time btw request and response " + elapsedTime);
            }
            catch (Exception e)
            {
                _globalContext.LogMessage("bad request");
                response = true;
            }
        }

        public static string getExtension(int agentid)
        {
            string extn = "";
            string URL = "https://epson.custhelp.com/services/rest/connect/v1.3/queryResults?query=select accounts.CustomFields.c.ctiextension from accounts where id = " + agentid;
            //string url = "";
            try
            {
                HttpWebRequest request = (HttpWebRequest)WebRequest.Create(URL);
                String encoded = System.Convert.ToBase64String(System.Text.Encoding.GetEncoding("ISO-8859-1").GetBytes("admin_rnt:epsonadmin454!"));
                request.Headers.Add("Authorization", "Basic " + encoded);
                WebResponse response = request.GetResponse();
                using (Stream responseStream = response.GetResponseStream())
                {
                    StreamReader reader = new StreamReader(responseStream, Encoding.UTF8);
                    string jsonResponseFromServer = reader.ReadToEnd();
                    dynamic dict = new JavaScriptSerializer().Deserialize<dynamic>(jsonResponseFromServer);
                    extn = dict["items"][0]["rows"][0][0].ToString();
                }
            }
            catch (Exception e)
            {
            }

            return extn;
        }

        #endregion
    }

    [AddIn("Workspace Factory AddIn", Version = "1.0.0.0")]
    public class WorkspaceAddInFactory : IWorkspaceComponentFactory2
    {
        #region IWorkspaceComponentFactory2 Members
        IGlobalContext _globalContext;
        /// <summary>
        /// Method which is invoked by the AddIn framework when the control is created.
        /// </summary>
        /// <param name="inDesignMode">Flag which indicates if the control is being drawn on the Workspace Designer. (Use this flag to determine if code should perform any logic on the workspace record)</param>
        /// <param name="RecordContext">The current workspace record context.</param>
        /// <returns>The control which implements the IWorkspaceComponent2 interface.</returns>
        public IWorkspaceComponent2 CreateControl(bool inDesignMode, IRecordContext RecordContext)
        {
            return new WorkspaceAddIn(inDesignMode, RecordContext, _globalContext);
        }

        #endregion

        #region IFactoryBase Members

        /// <summary>
        /// The 16x16 pixel icon to represent the Add-In in the Ribbon of the Workspace Designer.
        /// </summary>
        public Image Image16
        {
            get { return Properties.Resources.AddIn16; }
        }

        /// <summary>
        /// The text to represent the Add-In in the Ribbon of the Workspace Designer.
        /// </summary>
        public string Text
        {
            get { return "webServiceprod"; }
        }

        /// <summary>
        /// The tooltip displayed when hovering over the Add-In in the Ribbon of the Workspace Designer.
        /// </summary>
        public string Tooltip
        {
            get { return "WorkspaceAddIn Tooltip"; }
        }

        #endregion

        #region IAddInBase Members

        /// <summary>
        /// Method which is invoked from the Add-In framework and is used to programmatically control whether to load the Add-In.
        /// </summary>
        /// <param name="GlobalContext">The Global Context for the Add-In framework.</param>
        /// <returns>If true the Add-In to be loaded, if false the Add-In will not be loaded.</returns>
        public bool Initialize(IGlobalContext GlobalContext)
        {
            _globalContext = GlobalContext;
            return true;
        }

        #endregion
    }
}