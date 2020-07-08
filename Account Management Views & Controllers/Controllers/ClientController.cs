using System;
using System.Collections.Generic;
using System.Data;
using System.Data.Entity;
using System.Linq;
using System.Net;
using System.Web;
using System.Web.Mvc;
using System.Net.Mail;
using ASPSnippets.GoogleAPI;
using System.Web.Script.Serialization;

using Tyme.Models;
using Tyme.Controllers;
using System.Runtime.InteropServices;
using System.Net.Http;
using System.Web.UI.WebControls;
using System.IO;
using System.Web.WebSockets;
using System.Drawing;
using System.Text;

namespace TimeManager.Controllers
{
    public class ClientController : Controller
    {
        private TymeDataEntities db = new TymeDataEntities();
        // GET: Client
        public ActionResult Index()
        {
            return View();
        }

        // GET: Login
        public ActionResult Login()
        {
            if (Session["ResetView"] != null)
            {
                Response.Write("<script>alert('" + Session["ResetView"].ToString() + "')</script>");
                Session.Remove("ResetView");
                Session.Remove("validReset");
            }
            if (Session["ForgotPassword"] != null)
            {
                Response.Write("<script>alert('" + Session["ForgotPassword"].ToString() + "')</script>");
                Session.Remove("ForgotPassword");
            }

            if (Request.QueryString["error"] == "access_denied")
            {
                Response.Write("<script>alert('Error! Could not authorize a Google Account.')</script>");
                return RedirectToAction("Login");
            }
            else if (!string.IsNullOrEmpty(Request.QueryString["code"]))
            {
                string code = Request.QueryString["code"];
                string json = GoogleConnect.Fetch("me", code);
                GoogleProfile profile = new JavaScriptSerializer().Deserialize<GoogleProfile>(json);
                //scan the database for the email given
                foreach (User check in db.Users)
                {
                    if (check.email == profile.Email.ToString())
                    {
                        //found same email in our database, sign them in
                        Session["user"] = check;
                        if (check.image != null) setMySrcFromUser(check);
                        return RedirectToAction("Index");
                    }
                }
                //end of loop, didn't find a corresponding user
                //register our new user without a password
                //only can sign in with google
                //regular login will send you an email in order to register
                User newUser = new User();
                //only need an email but we'll try to make registration easier
                if (profile.Name.ToString() != null)
                {
                    newUser.name = profile.Name.ToString();
                }
                else
                {
                    newUser.name = "Google";
                }
                newUser.email = profile.Email.ToString();
                newUser.password = null;
                newUser.salt = null;
                newUser.image = null;
                newUser.isAdmin = "n";
                db.Users.Add(newUser);
                db.SaveChanges();
                foreach (User check in db.Users)
                {
                    if (check.email == newUser.email)
                    {
                        //found same email in our database, sign them in
                        Session["user"] = check;
                        if (check.image != null) setMySrcFromUser(check);
                        break;
                    }
                }
                return RedirectToAction("Index");
            }
            ViewData["googleClientId"] = "52904577299-5nh8vsemuuu6evd8v0c2bh0ivdr7po1p.apps.googleusercontent.com";

            return View();
        }

        // POST: Login
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Login(string email, string password, string Login, string Google)
        {
            if (Login != null)
            {
                foreach (User check in db.Users)
                {
                    PasswordStorage ps = new PasswordStorage();
                    if (check.email == email)
                    {
                        if (ps.VerifyPassword(check.salt + password, check.password))
                        {
                            Session["user"] = check;
                            if (check.image != null) setMySrcFromUser(check);

                            return RedirectToAction("Index");
                        }
                    }
                }
            }
            if(Google != null)
            {
                return RedirectToAction("Google");
            }
            return RedirectToAction("Login");
        }

        public ActionResult Google()
        {
            GoogleConnect.ClientId = "52904577299-5nh8vsemuuu6evd8v0c2bh0ivdr7po1p.apps.googleusercontent.com";
            GoogleConnect.ClientSecret = "AAantZfmV8k0YpAdI6KAFSEr";
            GoogleConnect.RedirectUri = "https://localhost:44311/Client/Login";
            GoogleConnect.Authorize("profile", "email");
            
            return RedirectToAction("Login");
        }
        // GET: Register
        public ActionResult Register()
        {
            return View();
        }

        // POST: Register
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Register([Bind(Include = "Id,name,email,password,isAdmin,image")] User user, HttpPostedFileBase fileToUpload)
        {
            if (ModelState.IsValid)
            {
                user.isAdmin = "n";
                if (fileToUpload != null)
                {
                    user = this.addMyImageToUser(user, fileToUpload); //Inserts image and returns user
                }
                string theSalt = CreateSalt();
                user.salt = theSalt;
                PasswordStorage ps = new PasswordStorage();
                user.password = ps.CreateHash(theSalt + user.password);
                db.Users.Add(user);
                db.SaveChanges();
                
                foreach (User check in db.Users)
                {
                    if (check.email.ToString() == user.email)
                    {
                        Session["user"] = check;
                        if (check.image != null) setMySrcFromUser(check);

                        return RedirectToAction("ShowAccount");
                    }
                }
                
            }
            return View();
        }

        public string CreateSalt()
        {
            Random r = new Random();
            // Decide how long the salt will be
            int strLength = r.Next(1, 10);

            var sb = new StringBuilder();

            for (int i = 0; i < strLength; i++)
            {
                // Decide whether to add an uppercase letter, a lowercase letter, or a number
                int whichType = r.Next(0, 3);
                switch (whichType)
                {
                    // Lower case letter
                    case 0:
                        sb.Append((char)(97 + r.Next(0, 26)));
                        break;
                    // Upper case letter
                    case 1:
                        sb.Append((char)(65 + r.Next(0, 26)));
                        break;
                    // Number
                    case 2:
                        sb.Append((char)(48 + r.Next(0, 10)));
                        break;
                }
            }

            return sb.ToString();
        }

        // GET: ShowAccount
        public ActionResult ShowAccount()
        {
            if (Session["user"] == null)
            {
                return new HttpStatusCodeResult(HttpStatusCode.BadRequest);
            }
            User currentUser = (User)Session["user"];
            User user = db.Users.Find(currentUser.Id);
            if (user == null)
            {
                return HttpNotFound();
            }
            if(user.password != null)
            {
                ViewData["isPasswordHasValue"] = "YES";
            }
            return View(user);
        }

        // POST: ShowAccount(Save Changes to Account)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult ShowAccount([Bind(Include = "Id,name,email,password,isAdmin,image")] User user, HttpPostedFileBase fileToUpload)
        {
            if (ModelState.IsValid)
            {
                if (fileToUpload != null)
                {
                    user = this.addMyImageToUser(user, fileToUpload); //Inserts image and returns user
                    this.setMySrcFromUser(user);
                }
                db.Entry(user).State = EntityState.Modified;
                db.SaveChanges();
            }
            return RedirectToAction("ShowAccount");
        }

        // POST: SaveChanges
        public ActionResult Logout()
        {
            Session.Remove("user");
            Session.Remove("userPPImage");
            return RedirectToAction("Login");
        }

        // GET: ForgotPassword
        public ActionResult ForgotPassword()
        {
            if(Session["ForgotPassword"] != null)
            {
                Response.Write("<script>alert('"+ Session["ForgotPassword"].ToString() +"')</script>");
                Session.Remove("ForgotPassword");
            }

            return View();
        }

        // POST: ForgotPassword
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult ForgotPassword(string email)
        {
            int theUserId = 0; //to re-assign a user Id if the email exists in our database
            ResetMe reset = new ResetMe();
            foreach (User check in db.Users)
            {
                if (check.email.ToString() == email)
                {
                    theUserId = check.Id;
                    reset.userId = theUserId;
                    break;
                }
            }

            if(theUserId != 0)
            {
                db.ResetMes.Add(reset);
                db.SaveChanges();

                try
                {
                    foreach (ResetMe resetMe in db.ResetMes)
                    {
                        if (resetMe.userId == theUserId)
                        {
                            SendEmail(email, resetMe.Code);
                            Session["ForgotPassword"] = "Success! An email was sent to you for a new password.";
                            return RedirectToAction("Login");
                        }
                    }
                }
                catch
                {
                    Session["ForgotPassword"] = "Failure! Email could not be sent.";
                    return RedirectToAction("ForgotPassword");
                }
            }
            else
            {
                Session["ForgotPassword"] = "Error! User not found.";
            }

            return RedirectToAction("ForgotPassword");
        }
        public ActionResult ResetView()
        {
            if (Request.QueryString["code"] != null)
            {
                int code = int.Parse(Request.QueryString["code"]);

                //get parameter "code" and try to find it in the database
                if (db.ResetMes.Find(code) != null)
                {
                    Session["validReset"] = db.ResetMes.Find(code);
                }
            }
            if(Session["ResetView"] != null)
            {
                Response.Write("<script>alert('" + Session["ResetView"].ToString() + "')</script>");
                Session.Remove("ResetView");
                Session.Remove("validReset");
            }

            return View();
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult ResetView(string password)
        { 
            if (ModelState.IsValid && Session["validReset"] != null)
            {
                ResetMe reset = (ResetMe)Session["validReset"];
                User editPassword = (User)db.Users.Find(reset.userId);
                if (editPassword != null)
                {
                    PasswordStorage ps = new PasswordStorage();
                    editPassword.password = ps.CreateHash(editPassword.salt + editPassword.password);

                    db.Entry(editPassword).State = EntityState.Modified;

                    ResetMe user = (ResetMe)Session["validReset"];
                    foreach (ResetMe deleting in db.ResetMes)
                    {
                        if (deleting.userId == user.userId)
                        {
                            db.ResetMes.Remove(deleting);
                        }
                    }

                    db.SaveChanges();
                    Session["ResetView"] = "Congrats! You've got a new password. Make sure to write it down somewhere.";
                    return RedirectToAction("Login");
                }
            }

            Session["ResetView"] = "Uh oh. Your password could not be reset.";
            return View();
        }
        public void SendEmail(string toEmail, int resetCode)
        {
            //Take an email attached to an account that needs a password reset. 
            //Add a new row to the forgotPassword database 
            //Write the link within our email message.
            //Instantiate a new MailMessage and setup the client, our SMTP.
            //return alerts if email doesn't exist or sending an email was not successful
            string Message = "Click here to reset your password: " +
            "https://localhost:44311/Client/ResetView?code=" + resetCode;

            // Credentials
            var credentials = new NetworkCredential("markesherriff@gmail.com", "skate101");

            // Mail message
            var mail = new MailMessage()
            {
                From = new MailAddress("markesherriff@gmail.com"),
                Subject = "It's Tyme to Reset your Password",
                Body = Message
            };

            mail.IsBodyHtml = true;
            mail.To.Add(new MailAddress(toEmail));

            // Smtp client
            var client = new SmtpClient()
            {
                Port = 587,
                DeliveryMethod = SmtpDeliveryMethod.Network,
                UseDefaultCredentials = false,
                Host = "smtp.gmail.com",
                EnableSsl = true,
                Credentials = credentials
            };
            client.Send(mail);
        }

        // POST:  Assign image Url to ViewData["src"] with a catalog 
        public void setMySrcFromUser(User user)
        {
            byte[] imgByte = (byte[])(user.image); //convert binary into byte
            string str = Convert.ToBase64String(imgByte); //convert byte to string

            Session["userPPImage"] = "data:Image/png;base64," + str; //send image url along with neccessary directory
        }

        // GET: Include a catalog and file to add to it, recieve the new catalog
        public User addMyImageToUser(User user, HttpPostedFileBase fileToUpload)
        {
            user.image = new byte[fileToUpload.ContentLength];
            fileToUpload.InputStream.Read(user.image, 0, fileToUpload.ContentLength);

            return user;
        }

        // GET: Dashboard
        public ActionResult Dashboard()
        {
            return View();
        }

        // GET: Insporation
        public ActionResult Inspiration()
        {
            return View();
        }
    }
}