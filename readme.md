# Markdown Pages for Slim Framework

This is a demo app that uses Slim Framework and Markdown (with front matter) to generate a page structure and push it into a template system. Presently, it uses smarty templates.


### TODO / Notes to myself

* Need to be able to tell individual templates what their siblings, parents, and children are
* Build a tree structure of the file/nodes, possibly using [Tree](https://github.com/nicmart/Tree)
* It might be beneficial to build a flat array with easily parsable slugs for the controller to use, and the node/tree hierarchy for templates to use. This is already sort of built in a very crappy way with `SlimDown->getFileStruct`