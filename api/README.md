# TODO
- ~~`HolidayAddRequest.php` --> Add all Federal States~~
- ~~`Enum\FederalState` --> Add all Federal States~~
- ~~Add Validation if neither "isGeneral" nor valid "appliesTo" have been found~~
- ~~Add Error if created Holiday exists, based on day, month and year~~
  - ~~implement `findByDateParams` in HolidayRepository~~
- ~~Add a PATCH to edit a holiday based on day month year~~
- Add Holiday Endpoints:
  - by date 
  - by Federal State (only applicable for this state, not "isGeneral")
