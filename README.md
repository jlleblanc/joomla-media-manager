Joomla Media Manager

# General information

This project is a rewrite of the Joomla 1.6 Media Manager to support features like Content Delivery Networks, thumbnail generation, and drag-and-drop image management.

## Proposed features

After a short period of brainstorming and initial community feedback in All Together As A Whole, I have concluded that the following need to be implemented in the new Joomla! Media Manager:

* AJAX interface, allowing drag'n'drop copying and moving of files across directories
* File and directory permissions editing, as many servers upload files with stupid permissions (like 0600) if the FTP layer is enabled
* Batch uploads, using a Flash / HTML widget
* oEmbed support. For example, give it a Flickr URL and it will fetch the respective image
* Upload from URL: Give the URL of a remote media resource and it will be transferred and stored in your site
* Automatic thumbnail generation support
* Basic image editing, using third party sites (SplashUp, Picnic, etc)
* Automatic integration with leading CDN (Amazon CloudFront, Limelight, Akamai)
* Batch upload/thumbnail API for 3PD to use
* A public JSON API for 3PD to use for remotely uploading images to the Joomla! site

## Proposed architecture

The Media Manager is currently a hermetically sealed black box. It neither allows extending it, not can it be easily used by 3PDs, limiting its value down to garbage level. If we want to succeed in providing a useful piece of core software we must have three objectives:

1. Modern Web 2.0 look and feel
2. Extensibility
3. Reusability

The first goal is surprisingly easy to achieve. After all, this is what we have been doing for our clients working as freelance developers. The other two areas are of major concern, as they require careful architecture planning.

## Web 2.0 look and feel

Think eXtplorer. Why is it successful? It presents the file system in a familiar “Explorer”-like way, a UI pattern which is readily used by all three major OS (Windows, Mac OS X, Linux). Further than that, it puts frequently used actions on the toolbar and all actions in an intuitive right-click reachable context menu. Copying a UI pattern which is so widely used and recognized is a straightforward path to success. Javascript – with mooTools 1.2+ – gives us everything necessary to replicate this UI pattern. Separating folder from file display, drag and drop move/copy actions, single click rename are all easily implemented and make the system a breeze for newbies and seasoned users alike.

That being said, we don't want to bloat the core with extra libraries, tons of CSS and image files, so let's try reusing what is readily available. mooTools, even though not my favourite library, can handle the task at hand. Bluestork has a ton of media type icons we must reuse. It also provides very straightforward CSS styling, with rounded corners and stuff. And since we'll be following the MVC pattern, other administrator templates may override the look and feel – and parts of the functionality – with their own. For example, an accessible template may choose to opt out of Web 2.0 functions. We'll have to keep that in mind when writing code. There must be a clear separation of the business logic and the presentation. In fact, we can confine business logic in helper classes, expose it in a RAW view through AJAX to the browser which will handle all presentation in Javascript.

A note here on background work required on the interface. Since we have to monitor the file system for changes, I suggest that we add an otherwise hidden XHR each time we load a directory's contents which will scan said directory for modified/deleted/added files and run any relevant synchronization tasks (generate new thumbnails, update cached media metadata, sync with a CDN). I have the technology to make sure it doesn't timeout, even when processing large directories with tons of files.

## Extensibility

Regarding the extensibility, I propose using a plug-in system, similar in concept to what Dioscouri is using for all of its components. To cut a long story short, I propose adding a “media” plug-in group which will hold a family of core and 3PD plug-ins:

* **File Type Processors.** One plug-in per MIME type which generates thumbnails. We can add generic plug-ins for the basic file types (BMP, JPG, PNG, GIF) and let 3PDs support exotic cases like video files, PDFs, Microsoft Office documents and what not.
* **File Actions.** Instead of hard-coding a limited amount of actions which can be applied to files, we can draw this actions list from a plug-in system. The actions we are going to implement are basic operations (rename, delete), permissions editing and image editing on third party sites. Each plug-in should be able to limit its availability on a specific set of MIME types. For example, I wouldn't like an “Edit with SplashUp” action appearing for an MP4 file.
* **Folder Actions.** Similar to the above, but for folders.
* **CDN Bridges.** The CDN bridge will be responsible for a. uploading files to the CDN and b. mapping a local relative path to a media file into a URL of this resource in the CDN. It will also expose a set of CDN-specific configuration options.

Since plug-ins can be installed, uninstalled, enabled and disabled we provide 3PDs and end users with maximum flexibility for their media management needs.

## Reusability

Writing reusable code is a major feature upgrade. Reusability can be focused on the following areas:

1. Embeddable. The Media Manager should be exposed as a JHTML element, available for all 3PDs to use in their component. Better yet, allow it to give feedback to the caller, e.g. acting as a file picker on steroids. This isn't very hard to achieve; we just have to add a RAW view.
2. Exposed local API. Exposing a reusable API for media uploads, thumbnail generation and media metadata querying.
3. CDN sync subscriptions. Why limit ourselves to files inside the “images” directory? Third party forum and social suites will certainly benefit from it. Not to mention any extension which does its own media management but would like to use a CDN.
4. CRON scheduling of media housekeeping. Since we'll have to monitor the file system for externally modified files and folders, we might as well make it into a job callable through CRON, so that it doesn't hog resources while working on the back-end.
5. Exposed remote API. As I wrote above, exposing a JSON API for remote uploads will provide 3PDs with a way to integrate the desktop and the web in ways we can't imagine. Why need a JSON API? Well, sometimes you don't want to give away your FTP credentials, but would like to allow lesser-privileged users (administrators, managers, even publishers and authors) to be able to upload bunches of images in a single go.