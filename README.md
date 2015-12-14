# image_pins
EE Fieldtype to add markers to images

Created to allow us to add markers to an image such as exhibits to a museum map with a clean and easy drag and drop UI in the control panel. Infowindows are separate channel entries which we plot onto our image to allow fully custom fields with no restrictions.

Image Template Example:

```
{exp:channel:entries}
	{image_map pin_template="/_map-pins/pin" pin_class="cd-single-point no-bullet"}
{/exp:channel:entries}
```

Infowindow Template Example:

```
{exp:channel:entries entry_id="{embed:pin_id}"}
	<div>
		<h2>{title}</h2>
		{if pin_section_img}
			<img src="{pin_section_img}" alt="{pin_img_desc}">
		{/if}
		{if pin_section_info}
			<p>{pin_section_info}</p>
		{/if}
		{pin_exhibit}
			<a href="{pin_exhibit:page_uri}">View Exhibit</a>
		{/pin_exhibit}
		<a href="#">Close</a>
	</div>
{/exp:channel:entries}
```

Infowindow code above is used as an embed, whereas the image template code can go anywhere in a standard template. Upon setting up the fieldtype, make sure you choose the chanel you wish to use as the markers within the fieldtype settings.