# List of all possible fields

The following is a list of typical fields already implemented. In the future, over time, I will write separate documentation for each.
All classes of these fields are located in the folder ```fields```

Type | Name | Description
----|---------|-------------
Standard | Boolean | Checkbox
Standard, Relations: hasOne | DropDown | Drop-down list
Standard | Email | Email
Standard | Field | Plain text
Standard | Group | Field group name
Standard | Hidden | Hidden field
Standard | Id | ID, primary key
Standard | Password | Password
Standard | RadiobuttonGroup | Radio buttons
Standard | StringField | String
Standard | Textarea | Text
Date and time | Time | Time
Date and time | Date | Date
Number | FloatField | Float number
Number | NumberField | Integer
Relations: hasMany | CheckboxList | Checkbox List
Relations: hasMany | HasManyMultipleInput | Nested multiform
Relations: hasMany | HasManySelect2 | Dropdown multi-list
Relations: hasOne | HasOneDepDrop | Dependent dropdown list [kartik-v/dependent-dropdown](https://github.com/kartik-v/dependent-dropdown)
Relations: hasOne | HasOneRadioList | Radio buttons for selecting items
Relations: hasOne | HasOneSelect2 | Drop-down list [kartik-v/yii2-widget-select2](https://github.com/kartik-v/yii2-widget-select2)
Relations: all | RelationsFilterField | Filtering by the presence or absence of related records
Relations: all | RelationValue | Display all field values of related records
Files | File | File
Files | Image | Image with thumbnails
Widget | AutoresizeTextarea | An auto-resizing text box. The package for this widget is here [execut/yii2-autosize-textarea](https://github.com/execut/yii2-autosize-textarea)
Widget | Editor | WYSIWYG-editor
Widget | PasswordWidget | Password with widget for checking password difficulty [kartik-v/yii2-password](https://github.com/kartik-v/yii2-password)
Widget | ProgressBar | Progress bar
Special | CompleteTime | Field for displaying the estimated time of completion of a process
Special | RawField | Field for displaying arbitrary HTML
Special | Translit | Transliterated value
Special | Action | Displaying action column for list of records