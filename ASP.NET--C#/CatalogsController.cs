using System;
using System.Collections.Generic;
using System.Data;
using System.Data.Entity;
using System.Drawing;
using System.Linq;
using System.Net;
using System.Web;
using System.Web.Mvc;
using FullFits_MarkSherriff.Models;

namespace FullFits_MarkSherriff.Controllers
{
    public class CatalogsController : Controller
    {
        private FullFitsDatabaseEntity db = new FullFitsDatabaseEntity();

        // GET: Catalogs
        public ActionResult Index()
        {
            return View(db.Catalogs.ToList());
        }

        // GET: Catalogs/Details/5
        public ActionResult Details(int? id)
        {
            if (id == null)
            {
                return new HttpStatusCodeResult(HttpStatusCode.BadRequest);
            }
            Catalog catalog = db.Catalogs.Find(id);
            if (catalog == null)
            {
                return HttpNotFound();
            }
            this.setMySrcFromCatalog(catalog);
            return View(catalog);
        }

        // GET: Catalogs/Create
        public ActionResult Create()
        {
            return View();
        }

        // POST: Catalogs/Create
        // To protect from overposting attacks, please enable the specific properties you want to bind to, for 
        // more details see https://go.microsoft.com/fwlink/?LinkId=317598.
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Create([Bind(Include = "Id,Title,Image,Price,Date,Description")] Catalog catalog, HttpPostedFileBase fileToUpload)
        {
            if (ModelState.IsValid)
            {
                this.addMyImageToCatalog(catalog, fileToUpload); //Custom Fuction
                db.Catalogs.Add(catalog);
                db.SaveChanges();
                return RedirectToAction("Index");
            }

            return View(catalog);
        }

        // GET: Catalogs/Edit/5
        public ActionResult Edit(int? id)
        {
            if (id == null)
            {
                return new HttpStatusCodeResult(HttpStatusCode.BadRequest);
            }
            Catalog catalog = db.Catalogs.Find(id);
            if (catalog == null)
            {
                return HttpNotFound();
            }
            return View(catalog);
        }

        // POST: Catalogs/Edit/5
        // To protect from overposting attacks, please enable the specific properties you want to bind to, for 
        // more details see https://go.microsoft.com/fwlink/?LinkId=317598.
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Edit([Bind(Include = "Id,Title,Image,Price,Date,Description")] Catalog catalog, HttpPostedFileBase fileToUpload)
        {
            if (ModelState.IsValid)
            {
                this.addMyImageToCatalog(catalog, fileToUpload); //Custom Fuction
                db.Entry(catalog).State = EntityState.Modified;
                db.SaveChanges();
                return RedirectToAction("Index");
            }
            return View(catalog);
        }

        // GET: Catalogs/Delete/5
        public ActionResult Delete(int? id)
        {
            if (id == null)
            {
                return new HttpStatusCodeResult(HttpStatusCode.BadRequest);
            }
            Catalog catalog = db.Catalogs.Find(id);
            if (catalog == null)
            {
                return HttpNotFound();
            }
            this.setMySrcFromCatalog(catalog);
            return View(catalog);
        }

        // POST: Catalogs/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public ActionResult DeleteConfirmed(int id)
        {
            Catalog catalog = db.Catalogs.Find(id);
            db.Catalogs.Remove(catalog);
            db.SaveChanges();
            return RedirectToAction("Index");
        }

        // POST:  Assign image Url to ViewData["src"] with a catalog 

        public void setMySrcFromCatalog(Catalog catalog)
        {
            byte[] imgByte = (byte[])(catalog.Image); //convert binary into byte
            string str = Convert.ToBase64String(imgByte); //convert byte to string

            ViewData["src"] = "data:Image/png;base64," + str; //send image url along with neccessary directory
        }

        // GET: Include a catalog and file to add to it, recieve the new catalog
        public Catalog addMyImageToCatalog(Catalog catalog, HttpPostedFileBase fileToUpload)
        {
            catalog.Image = new byte[fileToUpload.ContentLength];
            fileToUpload.InputStream.Read(catalog.Image, 0, fileToUpload.ContentLength);

            return catalog;
        }

        protected override void Dispose(bool disposing)
        {
            if (disposing)
            {
                db.Dispose();
            }
            base.Dispose(disposing);
        }
    }
}
