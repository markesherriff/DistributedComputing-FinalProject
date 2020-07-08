using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using ASPSnippets.GoogleAPI;
using System.Web.Script.Serialization;

namespace Tyme.Controllers
{
    public class GoogleProfile
    {
        public string Id { get; set; }
        public string Name { get; set; }
        public string Picture { get; set; }
        public string Email { get; set; }
        public string Verified_Email { get; set; }
    }
}