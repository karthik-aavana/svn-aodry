<?php
if ($access_settings[0]->note_split == "yes")
{
    if (implode('', $note1) != "" || implode('', $note2) != "")
    {
        ?>
        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">
            <tr>
                <td valign="top" style="width: 50%">
                    <?php
                    $no_of_tag = 0;
                    $break     = 0;
                    foreach ($note1 as $value)
                    {
                        if ($value == 'match')
                        {
                            $no_rec = 0;
                            foreach ($template1 as $val)
                            {
                                if ($no_rec < $no_of_tag)
                                {
                                    $no_rec++;
                                }
                                else
                                {
                                    $val[0]->content = str_replace(array(
                                            "\r\n",
                                            "\\r\\n",
                                            "\n",
                                            "\\n" ), " <br>", $val[0]->content);
                                    echo "<b> " . $val[0]->title . " : </b><br>" . $val[0]->content;
                                    $break           = 1;
                                    $no_of_tag++;
                                    break;
                                }
                            }
                        }
                        else
                        {
                            // $val=substr($value,0,1);
                            if ($break == 1 && $value != " ")
                            {
                                echo "<br>";
                                $break = 0;
                            }
                            echo $value . "<br/>";
                        }
                    }
                    ?>

                </td>
                <td valign="top">
                    <?php
                    $i          = 0;
                    $no_of_tag2 = 0;
                    $break1     = 0;
                    foreach ($note2 as $value)
                    {
                        if ($value == 'match')
                        {
                            $no_rec2 = 0;
                            foreach ($template2 as $val)
                            {
                                if ($no_rec2 < $no_of_tag2)
                                {
                                    $no_rec2++;
                                }
                                else
                                {
                                    $val[0]->content = str_replace(array(
                                            "\r\n",
                                            "\\r\\n",
                                            "\n",
                                            "\\n" ), " <br>", $val[0]->content);
                                    echo "<b> " . $val[0]->title . " : </b><br>" . $val[0]->content;
                                    $break1          = 1;
                                    $no_of_tag2++;
                                    break;
                                }
                            }
                        }
                        else
                        {
                            // $val=substr($value,0,1);
                            if ($break1 == 1 && $value != " ")
                            {
                                echo "<br>";
                                $break1 = 0;
                            }
                            echo $value . "<br/>";
                        }
                    }
                    ?>

                </td>
            </tr>
        </table>
        <?php
    }
}
else
{
    if (implode('', $note1) != "")
    {
        ?>
        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">
            <tr>
                <td valign="top" style="width: 50%">
                    <?php
                    $no_of_tag = 0;
                    $break     = 0;
                    foreach ($note1 as $value)
                    {
                        if ($value == 'match')
                        {
                            $no_rec = 0;
                            foreach ($template1 as $val)
                            {
                                if ($no_rec < $no_of_tag)
                                {
                                    $no__rec++;
                                }
                                else
                                {
                                    $val[0]->content = str_replace(array(
                                            "\r\n",
                                            "\\r\\n" ), " <br>", $val[0]->content);
                                    echo "<b> " . $val[0]->title . " : </b><br>" . $val[0]->content;
                                    $break           = 1;
                                    $no_of_tag++;
                                    break;
                                }
                            }
                        }
                        else
                        {
                            // $val=substr($value,0,1);
                            if ($break == 1 && $value != " ")
                            {
                                echo "<br>";
                                $break = 0;
                            }
                            echo $value . "<br/>";
                        }
                    }
                    ?>

                </td>
                <!-- <td valign="top" style="">
                <?php
                $i          = 0;
                $no_of_tag2 = 0;
                $break1     = 0;
                foreach ($note2 as $value)
                {
                    if ($value == 'match')
                    {
                        $no_rec2 = 0;
                        foreach ($template2 as $val)
                        {
                            if ($no_rec2 < $no_of_tag2)
                            {
                                $no_rec2++;
                            }
                            else
                            {
                                $val[0]->content = str_replace(array(
                                        "\r\n",
                                        "\\r\\n" ), " <br>", $val[0]->content);
                                echo "<b> " . $val[0]->title . " : </b><br>" . $val[0]->content;
                                $break1          = 1;
                                $no_of_tag2++;
                                break;
                            }
                        }
                    }
                    else
                    {
                        // $val=substr($value,0,1);
                        if ($break1 == 1 && $value != " ")
                        {
                            echo "<br>";
                            $break1 = 0;
                        }
                        echo $value . "<br/>";
                    }
                }
                ?>

                </td> -->
            </tr>
        </table>
        <?php
    }
}
?>

