# UI Guidelines

A (work-in-progress) guide to help in building bars, dropdowns, panels, fields and other UI elements within W admin interface.

## Main principles

### Stylesheets

- `base.css` is loaded everywhere.
- `back.css` is loaded everywhere except for the Edit view.
- Each view also loads its own stylesheet where base styles can be overidden, or specific elements be defined. 

Long stylesheets are divided via comments dividers. A summary of its sections can be found at the start of the file.

Apart from common reset, defined in `base.css`, never rely on a first level tag name to style elements but prefer descriptive classnames. 

Tag names might be used within a child selector (so they can benefit from the cascade) :
```css
/* instead of */
details { }
/* prefer */
.dropdown { }
.dropdown h2 { }
```

## Elements


### Common elements

`h1` and `h2` are used as `section` or `aside` titles, and are style the same way.  
`h3` and `h4` are used as sub-titles in various places, and are style differently according where they are used:
- Bookmarks
- Filters (both Home and Media)
- Dropdowns sections

### CSS layout variables
    
- `--radius` helps define the border-radius on buttons and inputs
- `--size-small` homogenize small sizes
- `--gap` separate sidebars & main panels, set to 0 for collapsed ones
- `--font-family`: main font preference
- `--spacing` set “white” space around and between elements, can be 4px to remind old style W look.
- `--reset-spacing` spacing adjustment (negates the spacing, allowing elements inside padded parent to go full bleed)
- `--half-spacing` narrow spacing
- `--double-spacing` double spacing 
- `--padding` used inside fields, inputs and buttons, can be closed to 3px to remind old style w look 
- `--reset-padding`, `--half-padding`, `--double-padding` same than `--spacing` adjustments

### CSS color variables
    
- `--main-color` should be light (light theme), used both as background and text color
- `--text-color` should be dark (light theme)
- `--text2-color` should be light (light theme)
- `--text3-color` should be dark (light theme)
- `--primary-background-color` should be light (light theme), visible erverywhere
- `--secondary-background-color` should be light (light theme), accent color used on navbars, dropdowns sections, table headings
- `--tertiary-background-color`, should be light (light theme), used on body, page metadata
- `--code-background-color`, `--code-color` should be contrasted enough
- `--outline-background-color`, `--outline-color` should be contrasted enough, highlights slected items, hovered table rows
- `--button-background-color`, `--button-color` should be contrasted enough
- `--input-background-color`, `--input-color` should be contrasted enough


### Flex layout helpers

Layouts, both macro and micro, _heavily_ rely on Flexbox, varying `flex-direction`, often using `--spacing` for `padding` and `gap`.

To allow less classnames and duplicates, two layout helpers are set:

- `.flexrow` helps to layout horizontal items, whichever is their inline/block natural behavior.
- `.flexcol` is the same for vertical layouts.

### Fields

Each field (input, submit, checkbox or radio) is wrapped widthin a `<p class="field"></p>`.
```html
<p class="field">
	<label for="myinput">label</label>
	<input type="text" name="myinput" id="myinput" value="">
</p>
```

Label comes first, then comes the input. CSS manages the order in case of checkboxes or radios.

When two fields whould be in a row, wrap them in a `.flexrow`:
```html
<div class="flexrow">
	<p class="field">
		<label for="myinput">label</label>
		<input type="text" name="myinput" id="myinput" value="">
	</p>
	<p class="field">
		<label for="myinput2">label</label>
		<input type="text" name="myinput2" id="myinput2" value="">
	</p>
</div>
```
Submit inputs or buttons are wrapped in a  `<p class="field submit-field"></p>`.
```html
<p class="field submit-field">
	<input type="submit" value="submit">
</p>
```
### Horizontal bar

Used for main top-bar and dropwdowns menu-bar.

Should have the class `.hbar`, and contain one or many `.hbar-section`.

Each section should contain one or many `.flexrow` to properly layout any kind of children :
- `div`: for grouping elements – which might not be useful
- `form > input(s)`: for update/post actions
- `a`: for links actions
- `span`: for help info 

### Dropdown menus

Like stated above, these menus should be put in a `.hbar-section` within a `.hbar`.
They are used in Home and Media.

They are based on `<details>` element – so that they can be closed and opened without js – that receive à `.dropdown` class.

The inner content is wrapped within a `.dropdown-content`.

```html
<details class="dropdown">
	<summary>Menu name</summary>
	<div class="dropdown-content">
		(dropdown sections)
	</div>
</details>
```
The content itself is wrapped within one or multiple `.dropdown-section` that can be a `div` or a `form`.

```html
<form class="dropdown-section" method="">
	<h2>Section title</h2>
	<div class="dropdown-section-content">
		(fields)
	</div>
</form>
```

### Collapsible panels

Every side panel is collapsible, thanks to a checkbox+label behavior.

```html
<aside class="toggle-panel-container">
	<input class="toggle-panel-toggle" type="checkbox" >
	<label class="toggle-panel-label">
	<div class="toggle-panel">
		<h2>Panel title</h2>
		<div class="toggle-panel-content">
			…
		</div>
	</div>
</aside>
```

On mobile view, the `label` switch to horizontal layout.

### Grid layouts

User and Profile view are displayed through a responsive grid layout. 

Each subsection is a `.grid-item`.

```html
<main class="grid">
	<div class="grid-item">
		<h2>Item title</h2>	
		…
	</div>
</main>
```

## Main screens

```
# Home
--------------------------------------------------
HEADER.hbar#topbar 
--------------------------------------------------
NAV.hbar#navbar
--------------------------------------------------
Aside ×| Aside ×| Section                
       |        |   > Deep search
       |        |   > Table | Graph | Map         
       |        |                        
       |        |                        
       |        |                        


# Media
--------------------------------------------------
HEADER.hbar#topbar 
--------------------------------------------------
NAV.hbar#navbar
--------------------------------------------------
Aside ×| Aside ×| Section                
       |        |   > Table
       |        | 
       |        |  
       |        |                        
       |        |         


# Edit
--------------------------------------------------
HEADER.hbar#topbar 
--------------------------------------------------
NAV.hbar#navbar
--------------------------------------------------
Aside ×| Section                       | × Aside
       |  > Tabs                       |
       |                               |
       |                               |
       |                               |
       |                               |


# Users
--------------------------------------------------
HEADER.hbar#topbar 
--------------------------------------------------
Add new user
  > Form
--------------------------------------------------
Users
  > Table
--------------------------------------------------
       

# Admin
--------------------------------------------------
HEADER.hbar#topbar 
--------------------------------------------------
NAV.hbar#navbar
--------------------------------------------------
Item    | Item    | Item    | Item    | Item     | 
Item    | Item    | 


# Profile
--------------------------------------------------
HEADER.hbar#topbar 
--------------------------------------------------
Item    | Item    | Item    


# Help
--------------------------------------------------
HEADER.hbar#topbar 
--------------------------------------------------
Nav    | Section
       |   
       |        
       |        
       |        
       |        


```

## Main structure

```
HEADER.hbar#topbar 

	div.hbar-section
		div | form | input | button | a | span
	
NAV.hbar#navbar

	div.hbar-section
		details.dropdown
			summary
			{div|form}.dropdown-content
				{div|form}.dropdown-section
					h2 (> a.help)
					p.field
						label
						input | select …
					p.field.submit-field
						input[type=submit] | a
						
MAIN
	
	? aside.toggle-panel-container
		label.toggle-panel-label + input[type=checkbox].toggle-panel-toggle
		.toggle-panel
			h2
				(a.help)
			div.toggle-panel-content	
				{div|form}.panel-section
					h3
						(a.help)
					{div|form}.panel-section-content
						(.tree | fieldset)
							(h4 | legend)
							(p.info)
							p.field
								label
								input | button | a
	
	? .home
		aside#bookmarks
		aside#filter
		section.pages
			h2
				text
				(.filter)
					.button
				.display-mode
					a
			div#searchbar
				…
			div.scrollable	
				table
					…

	? &.editor
		aside#leftbar
		div.tabs
			div.tab
				label + input[type=checkbox]
				div.tab-content
					textarea
		aside#rightbar

	? &.media
		aside (dirlist)
		aside (filters)
		section
			h2
			div.scroll
				table	| ul.gallery

	? &.grid
		div.grid-item
			h2
			p | h3 | form …

	? &.user
		section.new-user
			h2
			form
		section.all-users
			h2
			table		

	? &.main-info
		nav#toc
		section#doc
```


