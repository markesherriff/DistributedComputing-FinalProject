using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Web;
using System.Web.Mvc;
using FullFits_MarkSherriff.Models;

namespace FullFits_MarkSherriff.Controllers
{
    public class ClientController : Controller
    {
        private FullFitsDatabaseEntity db = new FullFitsDatabaseEntity();
        // GET: Catalog
        public ActionResult Index()
        {
            return View(db.Catalogs.ToList());
        }
        //POST: Add to Cart from Catalog Page

        // GET: Cart

        public ActionResult Cart()
        {
            return View(db.Catalogs.ToList());
        }

        public ActionResult Orders()
        {
            return View(db.Sales.ToList());
        }

        public ActionResult AccountPage()
        {
            return View();
        }

        public void logout()
        {

        }

        public void googleLogin()
        {
            
        }
    }
}