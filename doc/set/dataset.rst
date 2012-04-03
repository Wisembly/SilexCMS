DataSet
=======

The *DataSet* component works by checking every *TransientResponse* returned by a controller.

If this response contains a Twig block with a special name, it will get all matching entries in the database and assign them to a Twig variable with the same name .
