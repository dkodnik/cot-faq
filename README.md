
## Features
- Provides a page with a list of questions and or questions and answers
- Users and guests can ask questions if they have the proper rights priviledges
- Manage and submit questions and answers in the admin panel
- Order questions by manually setting positions and, configuration options for default ordering of questions with the same position value
- Categories through structures (optional)

## Requirements
- Cotonti 9.13 or greater

## Installation

1. Copy the plugin to your Cotonti plugins folder
2. Install it in Administration / Extensions

## Categories

The use of categories is optional and can be setup through `Administation / Structure / FAQ`. All categories will be created with guest as read only and will need to be configured under the structure other wise. You can setup category specific templates through structures configurations as well.

Question submission to the default page ( no category ) will follow rights based on the module wide rights under this modules rights configuration. 

## Templates

A couple of useful variables for template condition blocks to alter you templates:

- `{PHP.faq_uses_categories}`: a boolean that states whether the FAQ module is using categories
- `{PHP.faq_has_subcategories}`: a boolean that states whether the current FAQ category has subcategories
- `{PHP.faq_has_questions}`: a boolean that states whether the current FAQ category (even on the default page) has questions

You can find further documentation about available tags and blocks under the `/docs` directory included with this plugin. 
