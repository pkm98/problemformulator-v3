<!-- File: /app/View/Users/home.ctp -->

<h1>Problem Maps</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Description</th>
        <th>Created</th>
    </tr>

    <!-- Here is where we loop through our $Problem_maps array, printing out Problem_map info -->

    <?php foreach ($ProblemMaps as $problem_map): ?>
    <tr>
        <td><?php echo $problem_map['Problem_map']['id']; ?></td>
        <td>
            <?php echo $this->Html->link($problem_map['Problem_map']['title'],
array('controller' => 'problem_maps', 'action' => 'view', $problem_map['Problem_map']['id'])); ?>
        </td>
        <td><?php echo $problem_map['Problem_map']['description']; ?></td>
        <td><?php echo $problem_map['Problem_map']['created']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>

