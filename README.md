# Content Store

A simple content store that uses hashes to identify content. Similar to what I did for BioNames, but influenced by Ben Trask's ideas [Principles of Content Addressing] (https://bentrask.com/?q=hash://sha256/98493caa8b37eaa26343bbf73f232597a3ccda20498563327a4c3713821df892) - hat tip to [Jorrit Poelen's Preston](https://github.com/bio-guoda/preston) for the link.

Key idea is that we simply take a file and store it as a "blob", that is a file with no extension, where the file name is a hash of the file's contents (e.g., md5 or sha1). If we are using local storage (e.g., while debugging) I simply create a path to the file by creating a directory tree using the first three pairs of characters in the hash. Note that ideally we would create several different hashes of the file so that people can find it using their preferred hash. For example, Zenodo uses md5 to hash image files, whereas I used sha1 to hash PDFs in BioNames. 

As the file has no extension we need to be able to determine the type of file if we want to serve it up to a browser. I use PHP's `finfo_open(FILEINFO_MIME_TYPE)` to get the MIME type.


## Indexing 

Note also that the store is basically storing files "blind", there is no indexing of any associated metadata. I'm working towards having a very simple binary search index where file hashes are paired with other information, such as source URL, embedded metadata (e.g., PDF ID field), etc. so that we could have basic lookup by identifier. PDF indexing uses [mutool](https://www.mupdf.com/docs/manual-mutool-show.html).

## Links

If we have a public interface then could follow Ben Trask's model, See also related projects such as [Perkeep](https://perkeep.org). Trask uses links like this: `hash://sha256/6834b5440fc88e00a1e7fec197f9f42c72fd92600275ba1afc7704e8e3bcd1ee`, see [Hash URI Specification (Initial Draft)](https://github.com/hash-uri/hash-uri) for details.

## Things to think about

One thing to consider is how we store complex documents, and documents that link to other documents. For JATS XML with links to images (e.g., figures) we could process the XML to replace web links with hash links, so the XML and all source images are available via the content store. For other objects, such as an item in BHL, packaging the whole item as a PDF is probably going to be the easiest and most generic solution. Ideally everything we stored can be viewed natively by a web browser. Note that we can use fragment identifiers to jump to a specific location within a larger document.


