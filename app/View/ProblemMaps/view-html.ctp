<!-- File: /app/View/ProblemMaps/view.ctp -->

<div class="#modal" style="display: none;"></div>

<h1>Problem Map</h1>

<h2>Attributes</h2>
<table>
    <tr>
        <th>Id</th>
        <th>Type</th>
        <th>Description</th>
        <th>Created</th>
    </tr>

    <!-- Here is where we loop through our $ProblemMaps array, printing out Problem_map info -->

    <?php foreach ($ProblemMap['Attributes'] as $attribute): ?>
    <tr>
        <td><?php echo $attribute['id']; ?></td>
        <td><?php echo $attribute['type'] ?></td>
        <td><?php echo $attribute['description']; ?></td>
        <td><?php echo $attribute['created']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Decompositions</h2>
<table>
    <tr>
        <th>Parent Entity</th>
        <th>Id</th>
        <th>Created</th>
    </tr>

    <!-- Here is where we loop through our $ProblemMaps array, printing out Problem_map info -->

    <?php foreach ($ProblemMap['Decompositions'] as $decomposition): ?>
    <tr>
        <td><?php echo $decomposition['entity_id']; ?></td>
        <td><?php echo $decomposition['id'] ?></td>
        <td><?php echo $decomposition['created']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Entities</h2>
<table>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Type</th>
        <th>Created</th>
    </tr>

    <!-- Here is where we loop through our $ProblemMaps array, printing out Problem_map info -->

    <?php foreach ($ProblemMap['Entities'] as $entity): ?>
    <tr>
        <td><?php echo $entity['id']; ?></td>
        <td><?php echo $entity['name'] ?></td>
        <td><?php echo $entity['type']; ?></td>
        <td><?php echo $entity['created']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Links</h2>
<table>
    <tr>
        <th>Id</th>
        <th>From</th>
		<th>To</th>
        <th>Type</th>
        <th>Created</th>
    </tr>

    <!-- Here is where we loop through our $ProblemMaps array, printing out Problem_map info -->

    <?php foreach ($ProblemMap['Links'] as $link): ?>
    <tr>
        <td><?php echo $link['id']; ?></td>
        <td><?php echo $link['from_entity_id'] ?></td>
        <td><?php echo $link['to_entity_id'] ?></td>
        <td><?php echo $link['type'] ?></td>
        <td><?php echo $link['created']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Partial Orderings</h2>
<table>
    <tr>
        <th>Id</th>
        <th>Before</th>
		<th>After</th>
        <th>Created</th>
    </tr>

    <!-- Here is where we loop through our $ProblemMaps array, printing out Problem_map info -->

    <?php foreach ($ProblemMap['Links'] as $partial_ordering): ?>
    <tr>
        <td><?php echo $partial_ordering['id']; ?></td>
        <td><?php echo $partial_ordering['before_entity_id'] ?></td>
        <td><?php echo $partial_ordering['after_entity_id'] ?></td>
        <td><?php echo $partial_ordering['created']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>